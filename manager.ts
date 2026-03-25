import { spawn, type Subprocess } from "bun";

const PORT = 8080;
const queueConnection = process.env.QUEUE_CONNECTION ?? "database";

const sleep = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

type ProcessState = {
  command: string[];
  process: Subprocess | null;
  status: "running" | "stopped" | "starting";
  label: string;
  type: "persistent" | "recurring";
  intervalMs?: number;
  stopRequested: boolean;
};

const processes: Record<string, ProcessState> = {
  queue: {
    label: queueConnection === "redis" ? "Horizon" : "Queue Worker",
    command:
      queueConnection === "redis"
        ? ["php", "artisan", "horizon"]
        : ["php", "artisan", "queue:work", "--tries=3"],
    process: null,
    status: "stopped",
    type: "persistent",
    stopRequested: false,
  },
  schedule: {
    label: "Task Scheduler",
    command: ["php", "artisan", "schedule:work"],
    process: null,
    status: "stopped",
    type: "persistent",
    stopRequested: false,
  },
  imei_orders: {
    label: "IMEI Status Update",
    command: ["php", "artisan", "orders:update"],
    process: null,
    status: "stopped",
    type: "recurring",
    intervalMs: Number(process.env.MANAGER_IMEI_INTERVAL_SECONDS ?? 60) * 1000,
    stopRequested: false,
  },
  pix_status: {
    label: "Pix Status Sync",
    command: ["php", "artisan", "pix:consultar-status"],
    process: null,
    status: "stopped",
    type: "recurring",
    intervalMs: Number(process.env.MANAGER_PIX_INTERVAL_SECONDS ?? 60) * 1000,
    stopRequested: false,
  },
  currency_sync: {
    label: "Currency Exchange Sync",
    command: ["php", "artisan", "system:sync-currencies"],
    process: null,
    status: "stopped",
    type: "recurring",
    intervalMs: Number(process.env.MANAGER_CURRENCY_INTERVAL_SECONDS ?? 1800) * 1000,
    stopRequested: false,
  },
  dhru_sync: {
    label: "Dhru Providers Sync",
    command: ["php", "artisan", "dhru:sync-providers"],
    process: null,
    status: "stopped",
    type: "recurring",
    intervalMs: Number(process.env.MANAGER_DHRU_INTERVAL_SECONDS ?? 3600) * 1000,
    stopRequested: false,
  },
};

function startProcess(name: string) {
  const p = processes[name];
  if (!p || p.status === "running") return;

  console.log(`[MANAGER] Starting process: ${p.label}`);
  p.status = "starting";
  p.stopRequested = false;

  if (p.type === "persistent") {
    p.process = spawn(p.command, {
      stdout: "inherit",
      stderr: "inherit",
      onExit(_proc, exitCode) {
        console.log(`[MANAGER] Process ${p.label} exited with code ${exitCode}`);
        p.status = "stopped";
        p.process = null;
      },
    });

    p.status = "running";
    return;
  }

  p.status = "running";
  void runRecurringProcess(name);
}

async function runRecurringProcess(name: string) {
  const p = processes[name];
  if (!p || p.type !== "recurring") return;

  while (!p.stopRequested) {
    console.log(`[MANAGER] Executing recurring task: ${p.label}`);

    p.process = spawn(p.command, {
      stdout: "inherit",
      stderr: "inherit",
    });

    const exitCode = await p.process.exited;
    console.log(`[MANAGER] Recurring task ${p.label} exited with code ${exitCode}`);
    p.process = null;

    if (p.stopRequested) {
      break;
    }

    await sleep(p.intervalMs ?? 60000);
  }

  p.status = "stopped";
}

function stopProcess(name: string) {
  const p = processes[name];
  if (!p) return;

  console.log(`[MANAGER] Stopping process: ${p.label}`);
  p.stopRequested = true;

  if (p.process) {
    p.process.kill();
    p.process = null;
  }

  p.status = "stopped";
}

for (const name of Object.keys(processes)) {
  startProcess(name);
}

const server = Bun.serve({
  port: PORT,
  async fetch(req) {
    const url = new URL(req.url);
    const path = url.pathname;

    if (path === "/api/status") {
      const stats = Object.entries(processes).map(([name, data]) => ({
        key: name,
        label: data.label,
        status: data.status,
        command: data.command.join(" "),
        type: data.type,
        intervalMs: data.intervalMs ?? null,
      }));
      return Response.json({ success: true, processes: stats });
    }

    if (path === "/api/control" && req.method === "POST") {
      try {
          const body = await req.json();
          const { name, action } = body;
          
          if (!processes[name]) return Response.json({ error: "Unknown process" }, { status: 404 });

          if (action === "start") startProcess(name);
          else if (action === "stop") stopProcess(name);
          else if (action === "restart") {
            stopProcess(name);
            setTimeout(() => startProcess(name), 1000);
          } else {
              return Response.json({ error: "Invalid action" }, { status: 400 });
          }

          return Response.json({ success: true, name, action });
      } catch (e) {
          return Response.json({ error: "Invalid JSON" }, { status: 400 });
      }
    }

    return new Response("DHRU Management API Server", { status: 200 });
  },
});

console.log(`[MANAGER] DHRU Management Server running on port ${PORT}`);

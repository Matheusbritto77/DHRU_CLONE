import { spawn, type Subprocess } from "bun";

const PORT = 8080;

type ProcessState = {
  command: string[];
  process: Subprocess | null;
  status: "running" | "stopped" | "starting";
  label: string;
};

const processes: Record<string, ProcessState> = {
  queue: {
    label: "Queue Worker",
    command: ["php", "artisan", "queue:work", "--tries=3"],
    process: null,
    status: "stopped",
  },
  schedule: {
    label: "Task Scheduler",
    command: ["php", "artisan", "schedule:work"],
    process: null,
    status: "stopped",
  },
  imei_orders: {
    label: "IMEI Status Update",
    command: ["php", "artisan", "orders:update"],
    process: null,
    status: "stopped",
  },
  pix_status: {
    label: "Pix Status Sync",
    command: ["php", "artisan", "pix:consultar-status"],
    process: null,
    status: "stopped",
  },
  currency_sync: {
    label: "Currency Exchange Sync",
    command: ["php", "artisan", "system:sync-currencies"],
    process: null,
    status: "stopped",
  },
  dhru_sync: {
    label: "Dhru Providers Sync",
    command: ["php", "artisan", "dhru:sync-providers"],
    process: null,
    status: "stopped",
  },
};

function startProcess(name: string) {
  const p = processes[name];
  if (!p || p.status === "running") return;

  console.log(`[MANAGER] Starting process: ${p.label}`);
  p.status = "starting";
  
  p.process = spawn(p.command, {
    stdout: "inherit",
    stderr: "inherit",
    onExit(proc, exitCode, signalCode, error) {
      console.log(`[MANAGER] Process ${p.label} exited with code ${exitCode}`);
      p.status = "stopped";
      p.process = null;
    },
  });

  p.status = "running";
}

function stopProcess(name: string) {
  const p = processes[name];
  if (!p || !p.process) return;

  console.log(`[MANAGER] Stopping process: ${p.label}`);
  p.process.kill();
  p.status = "stopped";
  p.process = null;
}

// Global start for continuous processes
startProcess("queue");
startProcess("schedule");

// Periodic Syncs (if we want them managed by the same process)
// Or we just let schedule:work handle them if they are in the Kernel.

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

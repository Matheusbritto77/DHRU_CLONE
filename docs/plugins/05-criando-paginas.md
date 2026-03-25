# 📄 Criando Novas Páginas

## Quando Criar uma Página?

Crie uma nova página quando precisar de uma **URL exclusiva** no site. Exemplos:
- `/sobre` — Página "Sobre Nós"
- `/promocao` — Promoção especial
- `/black-friday` — Landing page sazonal
- `/termos` — Termos de uso e privacidade

---

## Passo a Passo

### 1. Acesse o Criador

1. Vá em **"Construtor de Páginas"** → **"Páginas do Site"**
2. Clique no botão **"Nova Página"** (canto superior direito)

### 2. Informações da Página

| Campo | O que preencher | Exemplo |
|---|---|---|
| **Título** | Nome da página | `Sobre Nós` |
| **Slug** | URL da página | `sobre` |
| **Página Ativa** | ✅ Se quiser que já fique online | |
| **Protegida (Core)** | ❌ Deixe desativado (páginas custom podem ser deletadas) | |

### 3. Meta Tags (SEO)

Adicione dados SEO clicando em **"+ Meta Tag"**:

| Propriedade | Conteúdo |
|---|---|
| `title` | `Sobre Nós - BR Server` |
| `description` | `Conheça nossa história e nossos diferenciais.` |

### 4. Monte a Página com Plugins

Na seção **"Blocos / Plugins da Página"**:

1. Clique em **"+ Adicionar Bloco/Plugin"**
2. Selecione **"Barra de Navegação"** (toda página precisa de um menu!)
3. Clique em **"+ Adicionar Bloco/Plugin"** novamente
4. Selecione o plugin de conteúdo que deseja (podem ser vários)
5. Adicione o **"Rodapé do Site"** por último
6. Use os botões ↑ ↓ para ajustar a ordem

### 5. Salve a Página

Clique em **"Criar"**. Sua página está no ar!

> 💡 **Acesse:** Depois de salvar, acesse `seusite.com/sobre` (ou o slug que escolheu) para ver o resultado. Caso a página tenha sido registrada como rota no sistema, ela estará disponível automaticamente.

---

## Estrutura Recomendada

Para manter a consistência visual, recomendamos que toda página siga esta ordem:

```
1. 📦 Barra Superior (Contatos)  — opcional
2. 📦 Barra de Navegação         — obrigatório
3. 📦 [Seus blocos de conteúdo]  — quantos quiser
4. 📦 Rodapé do Site             — obrigatório
```

---

## Páginas do Sistema vs Páginas Customizadas

| Característica | Página do Sistema | Página Customizada |
|---|---|---|
| **Exemplos** | Página Inicial (`/`) | Promoção, Sobre Nós |
| **Pode editar conteúdo?** | ✅ Sim | ✅ Sim |
| **Pode reordenar blocos?** | ✅ Sim | ✅ Sim |
| **Pode excluir?** | ❌ Não | ✅ Sim |
| **Pode desativar?** | ✅ Sim (fica offline) | ✅ Sim |

---

## Excluindo uma Página

1. Vá em **"Páginas do Site"**
2. Encontre a página desejada
3. Clique no ícone de **lixeira (🗑️)**
4. Confirme a exclusão

> ⚠️ Páginas marcadas como **"Core"** não terão o botão de exclusão. Isso é intencional para proteger páginas essenciais do sistema.

---

## Dúvidas Frequentes

**P: Posso usar o mesmo plugin em várias páginas?**
R: Sim! Plugins são reutilizáveis. Cada bloco em cada página pode ter suas próprias configurações.

**P: Se eu editar um plugin, muda em todas as páginas?**
R: Depende. Se você editou o **plugin** em si (Templates, CSS, JS), a mudança afeta todos os lugares. Se editou apenas as **configurações do bloco** dentro de uma página, afeta só aquela página.

**P: Posso ter páginas sem a Navbar ou Footer?**
R: Sim! Simplesmente não adicione esses blocos na página. Pode ser útil para landing pages minimalistas.

**P: Quantos plugins posso colocar em uma página?**
R: Não há limite técnico. Use quantos precisar.

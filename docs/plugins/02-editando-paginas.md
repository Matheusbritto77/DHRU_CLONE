# ✏️ Editando Páginas Existentes

## Acessando o Editor de Páginas

1. Acesse seu painel administrativo (ex: `seusite.com/admin`)
2. No menu lateral, procure o grupo **"Construtor de Páginas"**
3. Clique em **"Páginas do Site"**
4. Você verá a lista de todas as páginas cadastradas

---

## Entendendo a Lista de Páginas

Na tabela de páginas você encontra:

| Coluna | Significado |
|---|---|
| **Título** | Nome da página (ex: "Página Inicial") |
| **URL** | O endereço da página no site (ex: `/`) |
| **Blocos** | Quantos plugins estão ativos nessa página |
| **Core** | ✅ = Página do sistema (não pode deletar) |
| **Ativa** | ✅ = Página visível no site |

---

## Editando uma Página

1. Clique no ícone de **lápis (✏️)** ao lado da página que deseja editar
2. Você verá duas seções:

### Seção 1: Informações da Página
- **Título:** O nome interno da página
- **Slug (URL):** O endereço da página. Exemplo: `sobre` significa que a página ficará em `seusite.com/sobre`
- **Página Ativa:** Liga/desliga a página inteira
- **Meta Tags (SEO):** Título e descrição que aparecem no Google

### Seção 2: Blocos / Plugins da Página
Aqui é onde a mágica acontece! Você verá todos os plugins que compõem essa página listados na ordem em que aparecem:

```
📦 Barra Superior (Contatos)
📦 Barra de Navegação  
📦 Carrossel de Banners
📦 Texto Principal (Hero)
📦 Cartões de Funcionalidades
📦 Rodapé do Site
```

---

## Reordenando Plugins

Para mudar a ordem dos blocos na página:

1. Encontre o bloco que deseja mover
2. Use os **botões de seta (↑ ↓)** que aparecem ao lado de cada bloco
3. Clique em **"Salvar"** no final da página

> 💡 **Dica:** A ordem dos blocos aqui é exatamente a ordem que eles aparecem no site, de cima para baixo.

---

## Ocultando um Bloco

Não quer excluir um plugin, mas quer escondê-lo temporariamente?

1. Abra o bloco desejado clicando nele
2. Desative o toggle **"Visível"**
3. Salve a página

O plugin continuará cadastrado, mas não será exibido no site.

---

## Editando as Configurações de um Bloco

Cada bloco tem um painel de **"Configurações do Plugin"** com opções no formato chave → valor:

| Chave | Valor | O que faz |
|---|---|---|
| `email` | `contato@seusite.com` | Muda o e-mail exibido |
| `title` | `Meu Novo Título` | Muda o texto do título |
| `bg_color` | `#000000` | Muda a cor de fundo |
| `logo_src` | `logo3.jpg` | Muda a imagem da logo |

Para alterar:
1. Abra o bloco
2. Encontre a chave que deseja modificar
3. Altere o **valor** (coluna da direita)
4. Clique em **"Salvar"**

> ⚠️ **Cuidado:** Não altere o nome da **chave** (coluna da esquerda), apenas o **valor**. Alterar a chave pode quebrar a exibição do plugin.

---

## Adicionando um Novo Bloco

1. Role até o final da seção de blocos
2. Clique em **"+ Adicionar Bloco/Plugin"**
3. Selecione o plugin desejado no campo **"Plugin"**
4. As configurações padrão serão carregadas automaticamente
5. Ajuste o que precisar e salve

---

## Próximos Passos

- [Como personalizar um Plugin em detalhe →](03-personalizando-plugins.md)
- [Como criar um Plugin do zero →](04-criando-plugins.md)

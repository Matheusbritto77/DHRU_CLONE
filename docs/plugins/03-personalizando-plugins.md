# 🎨 Personalizando Plugins

## Acessando os Plugins

1. No painel Admin, vá em **"Construtor de Páginas"** → **"Plugins"**
2. Você verá todos os plugins instalados no sistema

---

## Entendendo a Lista de Plugins

| Coluna | Significado |
|---|---|
| **Nome** | Nome descritivo do plugin |
| **Slug** | Identificador único (não mudar!) |
| **Tipo** | Categoria: Navegação, Hero, Seção, Rodapé ou Personalizado |
| **Core** | ✅ = Plugin do sistema (não pode deletar) |
| **Ativo** | ✅ = Plugin disponível para uso |
| **Uso** | Em quantas páginas esse plugin está sendo usado |

---

## Editando um Plugin

Clique no **lápis (✏️)** ao lado do plugin. Você verá 4 abas:

### Aba 1: Informações
Dados básicos como nome, tipo e descrição. 

> 💡 Não altere o **Slug** de plugins do sistema, isso pode causar problemas.

### Aba 2: Template Blade
Este é o **código visual** do plugin — o HTML que define como ele aparece no site.

- Use `{{ $settings['nome_da_chave'] }}` para inserir valores que podem ser personalizados
- Qualquer alteração aqui afeta **todas as páginas** onde o plugin é usado

> ⚠️ **Atenção:** Editar o template requer conhecimento básico de HTML. Se não tiver certeza, prefira editar apenas as **Configurações Padrão** (Aba 4).

### Aba 3: Estilos & Scripts
- **CSS:** Estilos visuais extras (cores, animações, fontes)
- **JavaScript:** Comportamentos interativos (carrossel automático, contadores)

### Aba 4: Configurações Padrão ⭐
Esta é a aba mais importante para você! Aqui ficam os **valores padrão** que o plugin usa quando é adicionado a uma página.

---

## Guia de Personalização por Plugin

### 🔵 Barra Superior (topbar)

| Chave | O que muda | Exemplo |
|---|---|---|
| `email` | E-mail de contato | `suporte@minhaempresa.com` |
| `whatsapp` | Número exibido | `+55 (11) 99999-0000` |
| `whatsapp_link` | Link clicável | `https://wa.me/5511999990000` |
| `bg_color` | Cor de fundo da barra | `#1d1d1f` (preto) ou `#0071e3` (azul) |
| `text_color` | Cor do texto | `#ffffff` (branco) |

### 🔵 Barra de Navegação (navbar)

| Chave | O que muda | Exemplo |
|---|---|---|
| `logo_src` | Imagem da logo | `logo3.jpg` |
| `font_family` | Fonte do menu | `Inter`, `Arial`, `Roboto` |
| `bg_style` | Fundo do menu | `rgba(255,255,255,0.7)` (translúcido) |
| `login_text` | Texto do botão login | `Entrar` |
| `register_text` | Texto do botão registro | `Criar Conta` |
| `show_register` | Exibir botão de registro | `true` ou `false` |

### 🔵 Carrossel de Banners (carousel-banners)

| Chave | O que muda | Exemplo |
|---|---|---|
| `images` | Lista de imagens¹ | Veja nota abaixo |
| `interval_ms` | Velocidade de rotação (ms) | `4000` = 4 segundos |
| `bg_color` | Cor de fundo atrás das imagens | `#f5f5f7` |

> ¹ **Sobre as imagens:** As imagens do carrossel são gerenciadas na aba **"Personalização"** do menu principal. As imagens são salvas em `image/imagem1.png`, `imagem2.png` e `imagem3.png`.

### 🔵 Texto Principal (hero-text)

| Chave | O que muda | Exemplo |
|---|---|---|
| `title` | Título grande da página | `Bem-vindo ao nosso site!` |
| `subtitle` | Texto abaixo do título | `A melhor solução para você.` |
| `cta_primary_text` | Texto do botão principal | `Comece Agora` |
| `cta_secondary_text` | Texto do botão secundário | `Fazer Login` |
| `title_color` | Cor do título | `#1d1d1f` |
| `subtitle_color` | Cor do subtítulo | `#6e6e73` |
| `btn_primary_color` | Cor do botão principal | `#0071e3` |

### 🔵 Cartões de Funcionalidades (features-grid)

| Chave | O que muda | Exemplo |
|---|---|---|
| `section_title` | Título da seção | `Nossos Serviços` |
| `section_subtitle` | Subtítulo da seção | `O melhor para você.` |
| `cards` | Lista de cartões² | Veja nota abaixo |

> ² **Sobre os cartões:** Para editar os cartões individualmente (título, texto, ícone, cor), edite diretamente as **Configurações Padrão** do plugin na Aba 4. 

### 🔵 Rodapé do Site (footer)

| Chave | O que muda | Exemplo |
|---|---|---|
| `logo_src` | Logo no rodapé | `logo3.jpg` |
| `description` | Texto descritivo | `Sua empresa de confiança.` |
| `email` | E-mail de contato | `contato@seusite.com` |
| `whatsapp` | Número WhatsApp | `+55 (11) 99999-0000` |
| `copyright` | Texto de direitos | `Minha Empresa. Todos os direitos reservados.` |

---

## Dicas de Cores

| Cor | Código | Uso Recomendado |
|---|---|---|
| Preto Apple | `#1d1d1f` | Textos, fundos escuros |
| Cinza Claro | `#f5f5f7` | Fundos claros, cards |
| Azul Apple | `#0071e3` | Botões, links, destaques |
| Cinza Texto | `#6e6e73` | Subtítulos, descrições |
| Branco | `#ffffff` | Fundos, textos em fundo escuro |
| Verde | `#059669` | Ícones de suporte |
| Roxo | `#7c3aed` | Ícones especiais |

---

## Próximos Passos

- [Como criar um Plugin novo →](04-criando-plugins.md)
- [Como criar uma Página nova →](05-criando-paginas.md)

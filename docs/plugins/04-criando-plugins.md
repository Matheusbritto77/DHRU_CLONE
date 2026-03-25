# 🛠️ Criando um Novo Plugin

## Quando Criar um Plugin?

Crie um novo plugin quando quiser adicionar uma seção que **ainda não existe** no site. Exemplos:
- Uma seção de **depoimentos de clientes**
- Uma área de **perguntas frequentes (FAQ)**
- Um **banner promocional** para Black Friday
- Um **contador de estatísticas** animado
- Um **formulário de contato**

---

## Passo a Passo

### 1. Acesse o Criador

1. Vá em **"Construtor de Páginas"** → **"Plugins"**
2. Clique no botão **"Novo Plugin"** (canto superior direito)

### 2. Preencha as Informações

| Campo | O que preencher | Exemplo |
|---|---|---|
| **Nome** | Nome descritivo | `Seção de Depoimentos` |
| **Slug** | Identificador único (sem espaços) | `depoimentos` |
| **Tipo** | Categoria do plugin | `Seção de Conteúdo` |
| **Versão** | Versão do plugin | `1.0.0` |
| **Autor** | Quem criou | `Seu Nome` |
| **Descrição** | Para que serve | `Mostra depoimentos de clientes` |
| **Ativo** | ✅ Deixe ativado | |

### 3. Escreva o Template

Na aba **"Template Blade"**, escreva o HTML do seu plugin. Aqui vai um exemplo simples:

```html
<section style="background-color: {{ $settings['bg_color'] }};" class="py-20 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <h2 style="color: {{ $settings['title_color'] }};" class="text-4xl font-semibold mb-4">
            {{ $settings['title'] }}
        </h2>
        <p class="text-gray-500 text-lg font-light">
            {{ $settings['description'] }}
        </p>
    </div>
</section>
```

#### Regras do Template

- Use `{{ $settings['nome'] }}` para valores que podem ser editados depois
- Todo HTML válido funciona (divs, imagens, links, etc.)
- Classes do Tailwind CSS estão disponíveis (como `text-center`, `py-20`, `bg-white`)
- Ícones do Font Awesome estão disponíveis (como `<i class="fas fa-star"></i>`)

### 4. Configure os Valores Padrão

Na aba **"Configurações Padrão"**, adicione as chaves que você usou no template:

| Chave | Valor Padrão |
|---|---|
| `bg_color` | `#ffffff` |
| `title_color` | `#1d1d1f` |
| `title` | `O que nossos clientes dizem` |
| `description` | `Veja os depoimentos de quem já usa nossos serviços.` |

> 💡 **Importante:** Cada `{{ $settings['xxx'] }}` que você escreveu no template precisa ter uma chave correspondente aqui. Se faltar, o plugin não vai exibir o valor.

### 5. (Opcional) Adicione CSS e JavaScript

Na aba **"Estilos & Scripts"**:

- **CSS:** Estilos extras para o seu plugin
- **JavaScript:** Animações e interações (ex: um contador que anima ao aparecer na tela)

### 6. Salve o Plugin

Clique em **"Criar"**. Seu plugin agora está disponível para ser adicionado em qualquer página!

---

## Adicionando seu Plugin a uma Página

1. Vá em **"Páginas do Site"**
2. Edite a página desejada
3. Role até **"Blocos / Plugins da Página"**
4. Clique em **"+ Adicionar Bloco/Plugin"**
5. Selecione seu novo plugin
6. Ajuste as configurações se necessário
7. Arraste para a posição desejada
8. Salve!

---

## Exemplo Completo: Plugin de FAQ

### Template:
```html
<section class="py-16 px-4 bg-white">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-semibold text-center mb-10" style="color: {{ $settings['title_color'] }};">
            {{ $settings['title'] }}
        </h2>
        <div class="space-y-4">
            <details class="bg-gray-50 rounded-2xl p-6 cursor-pointer group">
                <summary class="font-medium text-lg text-gray-800 flex justify-between items-center">
                    {{ $settings['pergunta_1'] }}
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="mt-4 text-gray-500 font-light">{{ $settings['resposta_1'] }}</p>
            </details>
            <details class="bg-gray-50 rounded-2xl p-6 cursor-pointer group">
                <summary class="font-medium text-lg text-gray-800 flex justify-between items-center">
                    {{ $settings['pergunta_2'] }}
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <p class="mt-4 text-gray-500 font-light">{{ $settings['resposta_2'] }}</p>
            </details>
        </div>
    </div>
</section>
```

### Configurações Padrão:
| Chave | Valor |
|---|---|
| `title` | `Perguntas Frequentes` |
| `title_color` | `#1d1d1f` |
| `pergunta_1` | `Como funciona o serviço?` |
| `resposta_1` | `Nosso serviço é 100% automatizado...` |
| `pergunta_2` | `Qual o prazo de entrega?` |
| `resposta_2` | `O prazo varia conforme o serviço...` |

---

## Próximos Passos

- [Como criar uma Página nova →](05-criando-paginas.md)
- [Referência rápida de cores e ícones →](03-personalizando-plugins.md#dicas-de-cores)

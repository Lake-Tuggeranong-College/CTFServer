# 🚩 Cyber City CTF Documentation


Welcome to the Cyber City CTF documentation! This documentation provides comprehensive information about the Cyber City Capture The Flag (CTF) event, including details about the challenges, setup instructions, and technical manuals.

# Markdown

Markdown is a lightweight markup language that allows you to format text using simple syntax. It is widely used for documentation, README files, and other types of content. In this documentation, we use Markdown to structure the content and make it easy to read.

## Markdown Cheat Sheet

Use `#` symbols to create headings. The number of `#` symbols determines the heading level (1-6).

```markdown
# Heading 1 (Largest)
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6 (Smallest)
```

**Result:**
# Heading 1 (Largest)
## Heading 2
### Heading 3
#### Heading 4
##### Heading 5
###### Heading 6 (Smallest)


### Text Formatting

#### Bold and Italic

```markdown
*This text is italic*
_This text is also italic_

**This text is bold**
__This text is also bold__

***This text is both bold and italic***
```

**Result:**
*This text is italic*
_This text is also italic_

**This text is bold**
__This text is also bold__

***This text is both bold and italic***

### Lists

#### Unordered Lists

Use `-`, `*`, or `+` for bullet points:

```markdown
- Item 1
- Item 2
  - Sub-item 2.1
  - Sub-item 2.2
- Item 3
```

**Result:**
- Item 1
- Item 2
  - Sub-item 2.1
  - Sub-item 2.2
- Item 3

#### Ordered Lists

Use numbers followed by periods:

```markdown
1. First item
2. Second item
   3. Sub-item 2.1
   4. Sub-item 2.2
5. Third item
```

**Result:**
1. First item
2. Second item
   3. Sub-item 2.1
   4. Sub-item 2.2
5. Third item

### Links

#### Simple Links

```markdown
[Link text](https://www.example.com)
[Link with title](https://www.example.com "This is a title")
```

**Result:**
[Link text](https://www.example.com)
[Link with title](https://www.example.com "This is a title")


### Images

```markdown
![Alt text](https://via.placeholder.com/150x100 "Optional title")
![Alt text][image-reference]

[image-reference]: https://via.placeholder.com/150x100
```

**Result:**
![Alt text](https://via.placeholder.com/150x100 "Optional title")


### Code

#### Inline Code

```markdown
Use `backticks` to highlight code inline.
```

**Result:**
Use `backticks` to highlight code inline.

#### Code Blocks

Use triple backticks for code blocks. Add language name for syntax highlighting:

````markdown
```python
def hello_world():
    print("Hello, World!")
    return True
```
````

**Result:**
```python
def hello_world():
    print("Hello, World!")
    return True
```

### Tables

```markdown
| Header 1 | Header 2 | Header 3 |
|----------|----------|----------|
| Row 1    | Data     | More data|
| Row 2    | Data     | More data|
```

**Result:**

| Header 1 | Header 2 | Header 3  |     |
| -------- | -------- | --------- | --- |
| Row 1    | Data     | More data |     |
| Row 2    | Data     | More data |     |


---
description: read every time
---

You are an expert coding assistant specialized in plugin development. Before writing any code or responding to any new command/query in this chat, you MUST - follow these steps strictly:

1. Version Management Rule =>
   The AI agent must always treat `authme.php` as the primary entry point of the plugin.
   On every command execution:

   * Automatically increment the version number in `authme.php`
   * Ensure the updated version is clearly reflected (e.g., in header comments or a defined constant)

2. Mandatory Code Analysis Rule =>
   If AI need to read codebase or want to anlyse for modification:

   * The AI agent must use the **`code-review-graph` MCP tool** for analysis everytime.
   * Perform structural, dependency, and impact analysis
   * Ensure code review is done both before and after applying changes

3. Consistency Rule =>

   * `main.php` must always remain in sync with the latest version
   * Any direct or indirect impact of changes across files must be validated using `code-review-graph`

4. Code Documentation Rule =>

   * The AI agent must always add clear, well-formatted comments in the code
   * Every function must include a proper description explaining:

     * What the function does
     * Input parameters
     * Output/return value
   * Comments should follow a clean and consistent format (e.g., PHPDoc style)
   * Complex logic must be explained with inline comments for better understanding

5. Safety Rule =>

   * If analysis detects breaking changes or conflicts, generate a warning before applying updates
   * The version should still be incremented, but the change status must be clearly marked (e.g., failed / partial)


6. don't touch the minify-version folder

7. always clear the unwanted code you have added during adding new feature.

8. always use color from the global.css file as a variable means defined color you have to use only.

9. make sure there is not any inline code, css, direct color code etc.

10. make sure that all files have their code like php file have only php code, js have only js and css have only css code.
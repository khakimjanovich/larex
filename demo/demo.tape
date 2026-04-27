Output demo/demo.gif


Set FontSize 14
Set Width 1200
Set Height 700
Set Theme "Catppuccin Mocha"
Set Shell "zsh"
Set Framerate 24
Set PlaybackSpeed 1.0
Set TypingSpeed 60ms
Set CursorBlink false

# Bootstrap the shell
Env LAREX_GITHUB_TOKEN "$LAREX_GITHUB_TOKEN"
Hide
Type "cd /Users/khakimjanovich/Documents/dev/arch-engine"
Enter
Type "export PS1='$ '"
Enter
Type "clear"
Enter
Show

# --- Step 1: inspect a project ---
Sleep 800ms
Type "php artisan larex:inspect --project=."
Sleep 400ms
Enter
Sleep 3500ms

# --- Pause to let viewer read the output ---
Sleep 2000ms

Type "clear"
Enter
Sleep 500ms

# --- Step 2: pull a GitHub milestone ---
Type "php artisan larex:milestone khakimjanovich larex 4 --project=."
Sleep 400ms
Enter
Sleep 6000ms

# --- Pause to let viewer read the output ---
Sleep 3000ms

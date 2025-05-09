

export function console_log_sameline(msg) {
  if (process.stdout.isTTY) { // Only if terminal supports it
    process.stdout.write(msg);
    process.stdout.clearLine(0);
    process.stdout.cursorTo(0);
  } else {
    console.log(msg); // Fallback for non-TTY (e.g., redirected output)
  }
}
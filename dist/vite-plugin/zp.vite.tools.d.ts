/**
 * Writes a message to the current console line, overwriting previous content.
 * Useful for progress updates or status messages.
 * Falls back to console.log for non-TTY environments.
 * @param {string} msg - The message to display.
 */
export function console_log_sameline(...args: any[]): void;
/**
 * Executes a single file system task synchronously: create directory, copy, or write file.
 * @param {string} name - Task name for logging.
 * @param {string} todo - Task type: 'copy', 'mkdir', or 'file'.
 * @param {string} src  - Source path or file content (for 'file').
 * @param {string} dst  - Destination path.
 * @param {function|string} filter - Optional filter for copying files. Function or extension string.
 * @param {boolean} [verbose=false] - If true, logs more details.
 * @returns {boolean} True if task succeeded, false otherwise.
 */
export function exec_task(name: string, todo: string, src: string, dst: string, filter: Function | string, verbose?: boolean): boolean;
/**
 * Recursively copies a directory or file, applying a filter if provided.
 * @param {string} src - Source file or directory path.
 * @param {string} dest - Destination file or directory path.
 * @param {function} [filter] - Optional filter function (srcPath) => boolean.
 */
export function copyRecursiveSync(src: string, dest: string, filter?: Function): void;

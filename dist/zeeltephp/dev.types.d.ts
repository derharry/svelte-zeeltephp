export function var_dump(variable: any, title?: string, flag?: number): void;
export function is_string(variable: any): boolean;
export function is_number(variable: any): boolean;
export function is_number_any(number: any): boolean;
export function is_array(variable: any): variable is any[];
export function is_object(variable: any): boolean;
/**
 * Checks if a variable is nullish (undefined, null, 0, '0', or empty string).
 * @param {*} variable
 * @returns {boolean}
 */
export function is_nullish(variable: any): boolean;
/**
 * Checks if a variable is empty.
 * @param {*} variable
 * @returns {boolean}
 */
export function is_empty(variable: any): boolean;
/**
 * return the type or instance of _var
 * enhanced typeof() for like class names  *Event, ZP_*
 * @param {*} _var
 * @returns typeof(_var)
 */
export function get_typeof(_var: any, debug?: boolean): "string" | "number" | "bigint" | "boolean" | "symbol" | "undefined" | "object" | "function";
/**
 * Checks if a variable is of a specific type.
 * @param {string} type
 * @param {*} variable
 * @returns {boolean}
 */
export function is_typeof(type: string, variable: any, debug?: boolean): boolean;
export function get_instanceof(variable: any): void;

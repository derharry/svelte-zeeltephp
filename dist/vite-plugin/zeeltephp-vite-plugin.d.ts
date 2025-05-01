export function zeeltephp(mode: any): {
    name: string;
    configResolved(resolvedConfig: any): void;
    closeBundle: {
        sequential: boolean;
        order: string;
        handler(): Promise<void>;
    };
};

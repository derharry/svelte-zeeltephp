export function zeeltephp(mode: any): {
    name: string;
    config(config: any, { mode, command }: {
        mode: any;
        command: any;
    }): void;
    closeBundle: {
        sequential: boolean;
        order: string;
        handler(): Promise<void>;
    };
};

export function load({ params, fetch, url }: {
    params: any;
    fetch: any;
    url: any;
}): Promise<{
    res_pageserver: string;
    res_phplib: string;
    res_phplibsub: string;
    res_phpzplib: string;
    '+page.js': string;
} | {
    '+page.js': string;
}>;

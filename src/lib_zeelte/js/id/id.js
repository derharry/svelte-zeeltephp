
export function tinyid (size = 21) {
	const arr = new Uint8Array(size)
	crypto.getRandomValues(arr)
	let e = ""
	for (let i = 0; i < size; i++) {
		let n = 63 & arr[i]
		e += n < 36 ? n.toString(36) : n < 62 ? (n - 26).toString(36).toUpperCase() : n < 63 ? "_" : "-"
	}
	return e
}

export function uid() {
	try {
		return crypto.randomUUID()
	} catch {
		return tinyid(36)
	}
}

export function validAnchorID(toid) {
	if (typeof toid !== 'string') return toid
	let anchored = toid.toLowerCase()
	.trim()
	.replace(/[^\w\s-_]/g,  '') // remove invalid chars
	.replace(/\s+|\./g,    '-') // trim spaces
	.replace(/^-+|-+$/g,   '-') // trim hyphens
	if (/^\d/.test(anchored)) 
		anchored = 'h-' + anchored
	return anchored
}
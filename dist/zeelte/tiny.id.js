
export const tinyid = (size = 21) => {
	const arr = new Uint8Array(size);
	crypto.getRandomValues(arr);
	let e = "";
	for (let i = 0; i < size; i++) {
		let n = 63 & arr[i];
		e += n < 36 ? n.toString(36) : n < 62 ? (n - 26).toString(36).toUpperCase() : n < 63 ? "_" : "-"
	}
	return e;
};

export function uid (){
      let id = tinyid(36, true);
      return id;
}

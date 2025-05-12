/////
// src: https://codedgeekery.com/snippets/tinyid
/////
export const cg_getRandomValues = (array) => {
	for (let i = 0, l = array.length; i < l; i++) {
		array[i] = Math.floor(Math.random() * 256);
	}
	return array;
}
export const cg_tinyid = (size=10) => {
	let e = "";
	const r = getRandomValues( new Uint8Array(size) );
	while ( size-- ) {
		let n = 63 & r[size];
		e += n < 36 ? n.toString(36) : n < 62 ? (n - 26).toString(36).toUpperCase() : n < 63 ? "_" : "-"
	}
	return e
};


/////
// src https://stackoverflow.com/questions/3231459/how-can-i-create-unique-ids-with-javascript
/////
export function tinyid(length = 5, more = false) {
      let id;
      if (!more) {
            id = 'id' + (new Date()).getTime();
            id = "id" + Math.random().toString(16).slice(2)
            //console.log('tinyid(', id, ')');
      } else {
            id = Date.now().toString(36) + Math.random().toString(36).slice(2);
      }
      return id;
      //idea69173cbaca2 
}
export function uid (){
      let id = tinyid(36, true);
      //console.log('uid(', id, ')');
      return id;
}
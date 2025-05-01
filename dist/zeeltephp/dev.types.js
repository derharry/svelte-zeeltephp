// Identifier expected. 'void' is a reserved word that cannot be used here.ts(
//export function void() { }

export function var_dump(variable, title = "", flag = 0) {
      if (flag == 0) {
            console.log('***************')
            console.log('* var_dump() ', title) // typeof variable, get_typeof(variable));
            console.log("\t", 'variable:', "\t", variable)
            console.log("\t", 'typeof:',   "\t", typeof variable, get_typeof(variable, true))
            //console.log("\t", 'is_typeof( null ) :', "\t", is_typeof(null, variable, true))
            //console.log("\t", 'is_typeof("null") :', "\t", is_typeof('null', variable, true))
            //console.log("\t", 'is_typeof() :', "\t", is_typeof('undefined', variable, true))
            //console.log("\t", 'is_typeof(""):', "\t",  is_typeof('PointerEvent', variable, true))
            //console.log("\t", 'is_typeof(PE):', "\t",  is_typeof(PointerEvent, variable, true))
            //console.log("\t", 'is_object():', "\t",  is_object(variable))
      } 
      /*
      if (is_array(variable) || is_object(variable)) {
            console.log('  DUMP ...', typeof(variable), variable)
            Object.entries(variable).forEach(([_var, _val]) => {
                  let spacer = '   ';
                  for (let i=0; i<flag; i++) spacer += spacer
                  console.log(spacer, _val, _var)
            });

            /*
            Object.entries(variable).forEach(([_var, _val]) => {
                  if (!is_empty(_val)) {
                        let spacer = '';
                        for (let i=0; i<flag; i++) spacer += ''
                        if (!is_nullish(_val) && !is_empty(_val))
                              var_dump(_val, _var, flag+=1)
                        console.log('  ', spacer, _var, _val)
            }});
      }
      */
      
      if (flag == 0) {
            console.log('***************')
      }
      /*
      if (is_array(variable) || is_object(variable)) {
            console.log('  DUMP ...', typeof(variable), variable)
            Object.entries(variable).forEach(([_var, _val]) => {
                  let spacer = '';
                  for (let i=0; i<flag; i++) spacer += ''
                  console.log('  ', spacer, _var, _val)
                        /*
                        if (!is_nullish(_val)) {
                              //let spacer += space flag*4
                              let spacer = '';
                              for (let i=0; i<flag; i++)
                                    spacer += ' '
                              console.log(spacer, _var, _val)
                        }* /
            });
            console.log('*************************')
      }*/
}
/*
dump() { 
      console.log('---DUMP ZP_ApiRouter-----------------------------');
      Object.entries(this).forEach(([variable, value]) => {
          if (value !== undefined && value !== null) 
              console.log(variable, value);
          
      });
      console.log('---END DUMP ZP_ApiRouter-----------------------------');
}*/


export function is_string(variable)  { 
      return is_typeof('string', variable)  
}


export function is_number(variable) { 
      return is_typeof('number', variable)  &&  !isNaN(variable)
}

export function is_number_any(number) {
      return !isNaN(parseInt(number))
}


export function is_array(variable)  { 
      return Array.isArray(variable)
}


export function is_object(variable) { 
      return is_typeof('object', variable)  &&  !is_array(variable)
}



/**
 * Checks if a variable is nullish (undefined, null, 0, '0', or empty string).
 * @param {*} variable 
 * @returns {boolean}
 */
export function is_nullish(variable) {
      let return_value = false
      try {
            if (variable === undefined || variable === 'undefined'
                  || variable === null   || variable === 'null'
                  || variable === 0      || variable === '0'
                  || variable === ''
            ) return_value = true
      } catch (error) {
            console.error(error)
      } finally {
            //console.log(`is_nullish(${variable})`,return_value, typeof(variable))
      }
      return return_value;    
}


/**
 * Checks if a variable is empty.
 * @param {*} variable 
 * @returns {boolean}
 */
export function is_empty(variable) {
      let return_value = false
      try {
      if (is_nullish(variable)
            || is_array(variable)  && variable.length === 0
            || is_object(variable) && Object.keys(variable).length === 0
            || is_string(variable) && variable == ''
            || variable.toString() && variable == ''
      ) return_value = true
      } catch (error) {
      console.error(error)
      } finally {
      //console.log(`is_empty()`, typeof(variable), return_value, {variable})
      }
      return return_value;    
}

/**
 * return the type or instance of _var
 * enhanced typeof() for like class names  *Event, ZP_*
 * @param {*} _var 
 * @returns typeof(_var)
 */
export function get_typeof(_var, debug = false) {
            
            //console.log('get_typeof(', typeof(_var), ')', typeof(typeof(_var)))
            /*
            if (!is_nullish(_var)) return _var;
            if (typeof(_var) == 'object')
                  return _var?.constructor.name || null;

            /*
            //console.log('get_type_of(', _var, ')')
            //console.log('  -- typeof(', typeof(_var), ')', typeof(typeof(_var)))
            //console.log('  -- instanceof(', typeof(_var), ')', _var?.constructor.name || null)
            
            let x = instanceof _var;
            console.log('typeof(', typeof(_var), ')', typeof(typeof(_var)))
            if (Array.isArray(_var)) 
                  return 'array';
            if (typeof(variable) == 'object')
                  return variable.constructor
            */
      let response = typeof _var;
      try {
            if (debug) console.log('## get_typeof()')


            //let x1 = typeof _var
            //if (_var === null) response =  null
            //if (_var === undefined) return undefined
            //if (debug) console.log(' get_typeof()', _var, x1, _var == x1);
            //if (debug) console.log(' get_typeof()', typeof _var , typeof x1);
      }
      catch (error) {
            console.log(error)
      }
      if (debug) console.log('//', "\t", 'return', _var, typeof(_var));
      return response
}

/**
 * Checks if a variable is of a specific type.
 * @param {string} type 
 * @param {*} variable 
 * @returns {boolean}
 */
export function is_typeof(type, variable, debug = false) {
      let response = false;
      let _type = get_typeof(variable, debug);
      try {
            if (debug) console.log('## is_typeof()')
            if (debug) console.log("\t", type, typeof variable, _type, typeof type, typeof _type)
            
            // defaults
            response = type === variable
            if (!response) response = type == variable
            if (!response) response = type === typeof variable
            if (!response) response = type ==  typeof variable
            if (!response) response = type ==  _type

            // objects ??
            //if (typeof variable == 'object') {
            //      if (!response) response = variable instanceof type ? true : false
            //}
            // instances?
            // strings?
            // objects ??
            

            /*
            return;
            if (debug) console.log('## is_typeof()')
            if (debug) console.log("\t", type == variable, type, variable)
            if (debug) console.log("\t", type === variable, type, variable)
            if (debug) console.log("\t", type === variable, typeof type, typeof variable)
            if (debug) console.log("\t", type === typeof variable, typeof type, typeof variable)
                        
            response = type == variable
            if (!response) response = type === variable
            if (!response) response = type == typeof type
            if (!response) {
                  response = type == get_typeof(variable)
            }

            //if (type == get_typeof(type)) response = true
            //else if (typeof type == get_typeof(type)) response = true
            */
      }
      catch (error) {
            console.log(error)
      } 
      finally {
            if (debug) console.log("\t", 'return', response)
            return response;
      }
      if (debug) console.log("\t", 'return', "\t", response, type, get_typeof(variable), typeof type, typeof variable)
      if (debug) console.log('// is_typeof()')
      //if (debug) console.log('is_typeof()', variable, typeof(variable));
      //return typeof(_var);
            /*
            let _type_ze = get_typeof(variable, debug)
            let _type_js = typeof variable
            let response = typeof type == get_typeof(variable)
            
            if (debug) console.log('is_typeof()', _type_js, _type_ze, response)
            if (debug) console.log('is_typeof()',typeof _type_js, typeof _type_ze, response)
            return response
            /*
            let _type = get_typeof(variable, debug)
            let response = false;
            if (debug) console.log('is_typeof()', type, typeof type, _type, _type === type)
            if (type === typeof type) response = true;
            //if (debug) console.log('is_typeof()', typeof(type), typeof(_type), typeof _type === type)
            //return typeof variable === type 
            //response = _type === type ? true : false
            return response
            */
}

export function get_instanceof(variable) { 

}



/*

export function is_type_of(_type, _var) {
      console.log('is_type_of(', _type, ')', get_type_of(_var))
      return false;
      /*
      if (!value) return;
      let types = type.split('|');
      if (types.includes(get_type_of(value))) return true;            
      //if (!type && Object.entries(value).length > 0) type = 'object';
      //console.log(type.toString(), "\t", _type, "\t", _type === type, "\t", value)
      return type === typeof(type);
      * /
}


export function is_typeof(variable, type) {
      /*
      let match = false;
      match = is_instanceof(variable, type)      
      if (!match) match = type == get_instanceof(variable)
      if (!match) 
      return match
      * /
return false;
}

export function is_instanceof(variable, type) {
      /*
      if (typeof(type) === 'string') 
      let instance = get_instanceof(variable)
      return variable instanceof type;


      return instance;
      * /
return false;
}

export function get_instanceof(variable) {
      let instance;
      try {
            if (variable?.constructor.name)
                  instance = variable.constructor.name
            else {
                  console.log('unparsed get_instanceof(', variable, ')')
            }
      }
      catch (error) {
            console.error(error)
      }
      return instance;
}


export function zp_is_type_of(variable, type) {
      let response = false;
      try {
      }
      catch (error) {
            console.error(error)
      }
      return response;
}
*/
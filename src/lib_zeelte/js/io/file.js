import path from 'path';
import fs   from 'fs';
import fsp  from 'fs/promises';

/**
 * dir does not exist or error
 * @param {string} path 
 * @returns boolean
 */
export function isFile(path, consoleError = false) {
     try {
          const stats = fs.statSync(path);
          return stats.isFile();
     } catch (error) {
          consoleError && console.error(error)
          return false;
     }
}

export function readFile(filePath) {
     try {
          const file = fs.readFileSync(filePath, 'utf-8')
          return file
     } catch (error) {
          console.error('Error reading or parsing JSON file:', error);
          throw error;
     }
}


export function readJsonFile(filePath) {
     try {
          const file = fs.readFileSync(filePath, 'utf-8')
          const data = JSON.parse(file)
          return data
     } catch (error) {
          console.error('Error reading or parsing JSON file:', error);
          throw error;
     }
}

/**
 * Save an object as JSON to a file synchronously
 * @param {string} filePath - Path to the JSON file
 * @param {Object} data - JavaScript object to save
 */
export function writeJsonFile(filePath, data) {
  try {
    const jsonString = JSON.stringify(data, null, 2); // pretty print with 2 spaces
    fs.writeFileSync(filePath, jsonString, 'utf-8');
    console.log(`JSON saved successfully to ${filePath}`);
  } catch (error) {
    console.error('Error saving JSON file:', error);
    throw error;
  }
}
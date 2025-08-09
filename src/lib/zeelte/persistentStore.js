import { writable } from "svelte/store";


export function persistentStore_KeyValue(key, initialValue) {
     const stored = localStorage.getItem(key);
     const store  = writable(stored !== null ? stored : initialValue);

     return {
          subscribe: store.subscribe,
          set: (value) => {
               store.set(value);
               localStorage.setItem(key, value);
          },
          update: (updater) => {
               store.set(current => {
                    const updated = updater(current);
                    localStorage.setItem(key, updated);
                    return updated;
               });
          }
     };
}

export function persistentStore_Any(key, initialValue, { onSet, onUpdate } = {}) {
     const stored = localStorage.getItem(key);
     const store = writable(stored ? JSON.parse(stored) : initialValue);

     return {
          subscribe: store.subscribe,
          set: (value) => {
               store.set(value);
               localStorage.setItem(key, JSON.stringify(value));
               if (onSet) onSet(value);
          },
          update: (updater) => {
               store.set(current => {
                    const updated = updater(current);
                    localStorage.setItem(key, JSON.stringify(updated));
                    if (onUpdate) onUpdate(updated);
                    return updated;
               });
          }
     };
}
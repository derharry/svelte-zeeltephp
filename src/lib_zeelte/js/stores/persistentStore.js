//persistentStore.js
import { browser } from "$app/environment"
import { writable } from "svelte/store"

export function persistentStore(key, initialValue, { onSet, onUpdate } = {}) {
     if (key && typeof key.subscribe === "function" && key.isPersistent) {
          return key
     }
     
     let initial
     if (browser) {
          try {
               const stored = localStorage.getItem(key)
               initial = stored ? JSON.parse(stored) : initialValue
          } catch {
               initial = initialValue
               localStorage.setItem(key, JSON.stringify(initial))
          }
     } else {
          initial = initialValue
     }

     const store = writable(initial)
     return {
          subscribe: store.subscribe,
          set: (value) => {
               store.set(value)
               if (browser) localStorage.setItem(key, JSON.stringify(value))
               if (onSet) onSet(value)
          },
          update: (updater) => {
               store.update((current) => {
               const updated = updater(current)
               if (browser) localStorage.setItem(key, JSON.stringify(updated))
               if (onUpdate) onUpdate(updated)
                    return updated
               })
          },
          reset: () => {
               store.set(initialValue)
               if (browser) localStorage.setItem(key, JSON.stringify(initialValue))
               if (onSet) onSet(initialValue)
          },
          isPersistent: true,
     }
}
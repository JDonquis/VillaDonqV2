import { writable } from "svelte/store";

export const navStatus = writable({
    isContracted: false,
    navWidth: 240,
    
});

export function toggleMenu(objParams) {
    navStatus.update((current) => {
        let newStatus = !current.isContracted 
        if (newStatus == true) {
             return { ...current, isContracted: newStatus, navWidth: 60 };
        } else {
            return { ...current, isContracted: newStatus, navWidth: 240 };

        }
        console.log({current})
    });
}

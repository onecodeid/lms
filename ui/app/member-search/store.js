
import member from "./modules/member.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
    },

    mutations : {
    },

    modules : {
        member: member,
        system: system
    }
});

// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import { one_token, one_user, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        sexes: [{id:"M",text:"Laki-laki"},{id:"F",text:"Perempuan"}],
        selectedSex: null
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        }
    },
    actions: {
        
    }
}
// 1 => LOADING
// 2 => DONE
// 3 => ERROR

export default {
    namespaced: true,
    state: {
        
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
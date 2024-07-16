// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_expedition.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        expeditions: [],
        total_expedition: 0,
        
        expedition_name: 0,
        expedition_code: '',

        expedition_cod: 'N',
        expedition_cod_rate: 0,
        dialog_new: false,

        cod_rate_types: [{id:'Y',text:'Dihitung dari Harga Barang saja'},{id:'N',text:'Dihitung dari Harga Barang + Ongkir'}],
        selected_cod_rate_type: null
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_selected_cod_rate_type (state, v) {
            state.selected_cod_rate_type = v
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.expedition_name = context.state.expedition_name
                prm.expedition_cod = context.state.expedition_cod
                prm.expedition_cod_rate = context.state.expedition_cod_rate

                if (context.state.edit)
                    prm.expedition_id = context.rootState.expedition.selected_expedition.M_ExpeditionID

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    if (fn) {
                        fn()
                    } else
                        context.dispatch('expedition/search', {}, {root:true})
                    context.commit('set_common', ['dialog_new', false])
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}
// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_cod.js"
import { URL, UPL_URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        UPL_URL: UPL_URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',

        items: [],
        total_item: 0,
        selected_item: null,
        dialog_new: true,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: {},

        pendings: [],
        total_pending: 0,
        selected_pending: null,

        total: 0,
        total_actual: 0,
        note: '',
        date: new Date().toISOString().substr(0, 10),

        types: [{value:'A',text:'TRANSAKSI SUKSES'},{value:'R',text:'TRANSAKSI GAGAL'}],
        selected_type: null
    },
    mutations: {
        set_common(state, v) {
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

        update_search(state, search) {
            state.search = search
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_items(state, data) {
            state.items = data
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        set_expeditions(state, data) {
            state.expeditions = data
        },

        set_selected_expedition(state, val) {
            state.selected_expedition = val
        },

        set_total_expedition(state, v) {
            state.total_expedition = v
        },

        set_pendings (state, val) {
            state.pendings = val
        },

        set_selected_pending (state, val) {
            state.selected_pending = val
        },

        set_selected_type (state, val) {
            state.selected_type = val
        }
    },
    actions: {
        async accept(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token() }
                let hdata = { 
                    date: context.state.date, 
                    exp_id: context.state.selected_expedition.M_ExpeditionID,
                    note: context.state.note,
                    total: context.state.total_actual,
                    type: context.state.selected_type.value }
                let jdata = []
                for (let i of context.state.items) {
                    jdata.push({ so_id: i.L_SoID, note: '' })
                }
                prm.jdata = JSON.stringify(jdata);
                prm.hdata = JSON.stringify(hdata);

                let resp = await api.accept(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('cod/search', null, {root:true})
                    context.commit('cod/set_common', ['dialog_new', false], {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async reject(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token() }
                let hdata = { 
                    date: context.state.date, 
                    exp_id: context.state.selected_expedition.M_ExpeditionID,
                    note: context.state.note,
                    total: context.state.total_actual }
                let jdata = []
                for (let i of context.state.items) {
                    jdata.push({ so_id: i.L_SoID, note: '' })
                }
                prm.jdata = JSON.stringify(jdata);
                prm.hdata = JSON.stringify(hdata);

                let resp = await api.reject(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('cod/search', null, {root:true})
                    context.commit('cod/set_common', ['dialog_new', false], {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token(), cod: true }
                let resp = await api.search_expedition(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_expeditions", resp.data.records)
                    context.commit("set_total_expedition", resp.data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_pending(context) {
            context.commit("update_search_status", 1)
            try {
                let except = []
                for (let i of context.state.items)
                    except.push(i.L_SoID)

                let prm = {
                    search: context.state.query,
                    expedition_id: context.state.selected_expedition?context.state.selected_expedition.M_ExpeditionID:0,
                    except: JSON.stringify(except),
                    token: one_token()
                }

                let resp = await api.search_pending(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_pendings", resp.data.records)
                    context.commit("set_common", ['total_pending', resp.data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}
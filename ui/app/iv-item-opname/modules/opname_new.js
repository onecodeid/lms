// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_opname.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_opname: {},
        dialog_new: false,
        dialog_item: false,

        opname_number: "",
        opname_date: new Date().toISOString().substr(0, 10),
        opname_note: "",

        warehouses: [],
        selected_warehouse: null,

        items : [],
        total_item : 0,
        selected_item : {}
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

        set_object(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        set_selected_opname(state, val) {
            state.selected_opname = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_dialog_new (state, v) {
            state.dialog_new = v
        },

        set_items (state, v) {
            state.items = v
        },

        set_selected_item (state, v) {
            state.selected_item = v
        },

        set_items_av (state, v) {
            state.items_av = v
        },

        set_selected_item_av (state, v) {
            state.selected_item_av = v
        }
    },
    actions: {
        async save(context) {
            
            try {
                let hdata = {opname_date:context.state.opname_date,opname_note:context.state.opname_note,opname_warehouse:context.state.selected_warehouse.warehouse_id}
                let items = []
                for (let i of context.state.items)
                    items.push({item_id:i.M_ItemID,curr_qty:i.curr_qty,adjust_note:i.adjust_note})

                let prm = {token : one_token(), 
                            jdata : JSON.stringify(items),
                            hdata : JSON.stringify(hdata)
                        }

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("opname/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_detail(context) {
            context.commit("set_common", ["search_status", 1])
            try {
                
                let prm = {token : one_token(), 
                            opname_id: context.rootState.opname.selected_opname.I_OpnameID }
                let resp = await api.search_detail(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_items", resp.data.records)
                    context.commit("set_common", ['total_item', resp.data.total])
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("set_common", ["search_status", 1])
            try {
                let prm = {token : one_token()}
                let resp = await api.search_item(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_items", resp.data.records)
                    context.commit("set_common", ['total_item', resp.data.total])
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_warehouse(context) {
            let prm = {
                search: '',
                page: 1
            }

            return context.dispatch("system/postme", {
                url: "master/warehouse/search_dd",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['warehouses', d.records])
                    return d
                }
            }, { root: true })
        }
    }
}
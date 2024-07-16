// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_adjusttransfer.js"
import { one_token, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        search_status: 0,
        search_error_message: '',
        search: '',
        dialogAdjust: false,
        dialogTransfer: false,
        inAction: false,
        
        items: [],
        total_item: 0,
        total_item_page: 0,
        selected_item: {},

        warehouses: [],
        selectedWarehouse: null,
        transferQty: 0,
        adjustQty: 0,

        current_page: 1
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

        update_items(state, data) {
            state.items = data
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_item(state, val) {
            state.total_item = val
        },

        update_total_item_page(state, val) {
            state.total_item_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    for (let n in data.records) {
                        for (let m in data.records[n].stocks) {
                            data.records[n].stocks[m].adjust = false
                            data.records[n].stocks[m].transfer = false
                        }
                    }

                    context.commit("update_items", data.records)
                    context.commit("update_total_item", data.total)
                    context.commit("update_total_item_page", data.total_page)

                    context.commit("set_object", ["inAction", false])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async searchWarehouse(context) {
            let prm = {
                search: '',
                page: 1
            }

            context.dispatch("system/postme", {
                url: "master/warehouse/search_dd",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['warehouses', d.records])
                }
            }, { root: true })
        },

        async transferItem(context, d) {
            let prm = {
                warehouse_from: d.warehouse_id,
                warehouse_to: context.state.selectedWarehouse.warehouse_id,
                qty: context.state.transferQty,
                item: context.state.selected_item.item_id
            }

            let rst =
            context.dispatch("system/postme", {
                url: "inventory/adjusttransfer/transfer",
                prm: prm,
                callback: function(dd) {
                    context.dispatch("search", {})
                    return dd
                }
            }, { root: true })

            return rst
        },

        async adjustItem(context, d) {
            let prm = {
                warehouse_from: d.warehouse_id,
                warehouse_to: 0,
                qty: context.state.adjustQty,
                item: context.state.selected_item.item_id
            }

            let rst =
            context.dispatch("system/postme", {
                url: "inventory/adjusttransfer/adjust",
                prm: prm,
                callback: function(dd) {
                    context.dispatch("search", {})
                    return dd
                }
            }, { root: true })

            return rst
        }
    }
}
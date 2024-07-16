// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_omzet_product.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        omzet_products: [],
        total_omzet_product: 0,
        total_omzet_product_page: 0,
        selected_omzet_product: {},

        qty_products: [],

        areas: [],

        types: [{text:'ITEM & PAKET',value:'A'},{text:'ITEM',value:'I'},{text:'PAKET',value:'P'}],
        selected_type: {text:'ITEM & PAKET',value:'A'},

        users: [],
        selected_user: null,

        omzet_high: 100,
        qty_high: 100,

        current_page: 1,
        sdate: moment().subtract(30, 'days').format('YYYY-MM-DD'),
        edate: new Date().toISOString().substr(0, 10),
        dtx: false,

        selected_item: null
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

        update_omzet_products(state, val) {
            state.omzet_high = 100
            if (val.length > 0)
                state.omzet_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.omzet_high) * 100)
            state.omzet_products = val
        },

        update_qty_products(state, val) {
            state.qty_high = 100
            if (val.length > 0)
                state.qty_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.qty_high) * 100)
            state.qty_products = val
        },

        set_selected_omzet_product(state, val) {
            state.selected_omzet_product = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_omzet_product(state, val) {
            state.total_omzet_product = val
        },

        update_total_omzet_product_page(state, val) {
            state.total_omzet_product_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_sdate(state, val) {
            state.sdate = val
        },

        set_edate(state, val) {
            state.edate = val
        },

        set_dtx(state, val) {
            state.dtx = val
        },

        set_selected_type (state, val) {
            state.selected_type = val
        },

        set_selected_item (state, val) {
            state.selected_item = val
        },

        set_areas (state, val) {
            state.areas = val
        }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_omzet_products", resp.data)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_area(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.item_id = context.state.selected_item.item_id
                let resp = await api.search_area(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_areas', resp.data)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_qty(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                let resp = await api.search_qty(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_qty_products", resp.data)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }

        // async del(context, prm) {
        //     context.commit("update_search_status", 1)
        //     try {
        //         prm.token = one_token()
        //         prm.id = context.state.selected_omzet_product.M_ExpenseID

        //         let resp = await api.del(prm)
        //         if (resp.status != "OK") {
        //             context.commit("update_search_status", 3)
        //             context.commit("update_search_error_message", resp.message)
        //         } else {
        //             context.commit("update_search_status", 2)
        //             context.commit("update_search_error_message", "")
                    
        //             context.dispatch('search', {})
        //         }
        //     } catch (e) {
        //         context.commit("update_search_status", 3)
        //         context.commit("update_search_error_message", e.message)
        //         console.log(e)
        //     }
        // }
    }
}
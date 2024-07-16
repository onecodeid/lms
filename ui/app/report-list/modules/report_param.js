// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_report.js"
import { one_token, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        token: one_token(),
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',

        report_url: '',

        dialog: {
            'fin-001': false,
            'fin-002': false,
            'fin-003': false,
            'fin-004': false,
            'fin-005': false,
            'iv-001': false,
            'wh-001': false,
            'wh-002': false,
            'wh-005': false,
            'sales-002': false,
            'sales-004': false,
            'sales-005': false,
            'sales-006': false,
            'sales-008': false,
            'sales-009': false,
            'sales-010': false,
            'sales-011': false,
            'master-001': false
        },
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),

        admins: [],
        selected_admin: null,

        customers: [],
        selected_customer: null,
        search_customer: '',

        customer_levels: [],
        selected_customer_level: null,

        orders: [],
        selected_order: null,

        provinces: [],
        selected_province: null,
        cities: [],
        selected_city: null,

        items: [],
        selected_item: null,
        search_item: '',

        item_types: [{ value: 'N', text: 'Item' }, { value: 'Y', text: 'Paket' }],
        selected_item_type: null,

        expeditions: [],
        selected_expedition: null
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

        set_dialog(state, v) {
            let x = state.dialog;
            x[v[0]] = v[1];
            state.dialog = x
        },
        update_search_error_message(state, msg) { state.search_error_message = msg },
        update_search(state, search) { state.search = search },

        set_admins(state, v) { state.admins = v },
        set_selected_admin(state, v) { state.selected_admin = v },

        set_customers(state, data) { state.customers = data },
        set_selected_customer(state, val) { state.selected_customer = val },

        set_customer_levels(state, data) { state.customer_levels = data },
        set_selected_customer_level(state, val) { state.selected_customer_level = val },

        set_orders(state, data) { state.orders = data },
        set_selected_order(state, val) { state.selected_order = val },

        set_provinces(state, data) { state.provinces = data },
        set_selected_province(state, val) { state.selected_province = val },

        set_cities(state, data) { state.cities = data },
        set_selected_city(state, val) { state.selected_city = val },

        set_expeditions(state, data) { state.expeditions = data },
        set_selected_expedition(state, val) { state.selected_expedition = val },

        set_items(state, data) { state.items = data },
        set_selected_item(state, val) { state.selected_item = val },

        set_selected_item_type(state, val) { state.selected_item_type = val }
    },
    actions: {
        async search_admins(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token() }
                let resp = await api.search_admins(prm)
                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_admins", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_customer(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token(), search: context.state.search_customer }
                let resp = await api.search_customer(prm)

                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_customers", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_customer_level(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token() }
                let resp = await api.search_customer_level(prm)

                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_customer_levels", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_province(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token(), search: context.state.search_province }
                let resp = await api.search_province(prm)
                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_provinces", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_city(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token(), search: context.state.search_city, province_id: context.state.selected_province.M_ProvinceID }
                let resp = await api.search_city(prm)
                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_cities", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_expedition(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token() }
                let resp = await api.search_expedition(prm)
                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_expeditions", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_item(context) {
            context.commit("set_common", ['search_status', 1])
            try {
                let prm = { token: one_token(), search: '', page: -1 }
                let resp = await api.search_item(prm)
                if (resp.status != "OK") {
                    context.dispatch('search_status_not_ok', resp)
                } else {
                    context.dispatch('search_status_ok')
                    context.commit("set_items", resp.data.records)
                }
            } catch (e) {
                context.dispatch('search_status_not_ok', e)
            }
        },

        async search_status_not_ok(context, d) {
            context.commit("set_common", ['search_status', 3])
            context.commit("update_search_error_message", d.message)
        },

        async search_status_ok(context) {
            context.commit("set_common", ['search_status', 2])
            context.commit("update_search_error_message", "")
        }
    }
}
// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_customer.js"
import { one_token, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        token: one_token(),
        search_status: 0,
        search_error_message: '',
        search: '',

        customers: [],
        total_customer: 0,
        total_customer_page: 0,
        selected_customer: {},

        search_province_status: 0,
        provinces: [],
        selected_province: null,

        search_city_status: 0,
        cities: [],
        selected_city: null,

        selected_level: null,

        current_page: 1,
        report_url: '',

        dialog_transfer: false,
        transfers: [],
        users: [],
        selected_user_from: null,
        selected_user_to: null,
        transfer_types: [{ id: "A", text: "Semua Customer" }, { id: "S", text: "Customer Terpilih" }],
        selected_transfer_type: null
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

        update_customers(state, data) {
            state.customers = data
        },

        set_selected_customer(state, val) {
            state.selected_customer = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_customer(state, val) {
            state.total_customer = val
        },

        update_total_customer_page(state, val) {
            state.total_customer_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_selected_level(state, val) {
            state.selected_level = val
        },

        set_provinces(state, v) { state.provinces = v },
        set_selected_province(state, v) { state.selected_province = v },

        set_cities(state, v) { state.cities = v },
        set_selected_city(state, v) { state.selected_city = v }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.page = context.state.current_page
                prm.search = context.state.search
                prm.city = context.state.selected_city ? context.state.selected_city.M_CityID : 0
                prm.province = context.state.selected_province ? context.state.selected_province.M_ProvinceID : 0
                prm.level = 0
                prm.user_id = !!context.state.selected_user_from ? context.state.selected_user_from.user_id : 0
                if (context.state.selected_level)
                    prm.level = context.state.selected_level.M_CustomerLevelID
                let resp = await api.search(prm)
                console.log(resp)
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

                    context.commit("update_customers", data.records)
                    context.commit("update_total_customer", data.total)
                    context.commit("update_total_customer_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context, prm) {
            context.commit("update_search_status", 1)
            try {
                console.log(prm)
                prm.token = one_token()
                let resp = await api.del(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_province(context) {
            context.commit("set_common", ["search_province_status", 1])
            try {
                let prm = { token: one_token() }
                let resp = await api.search_province(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ['search_province_status', 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_province_status", 2])
                    context.commit("update_search_error_message", "")

                    context.commit("set_provinces", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_province_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_city(context) {
            context.commit("set_common", ["search_city_status", 1])
            try {
                let prm = { token: one_token(), search: context.state.search_city, province_id: context.state.selected_province.M_ProvinceID }
                let resp = await api.search_city(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_city_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_city_status", 2])
                    context.commit("update_search_error_message", "")

                    context.commit("set_cities", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_users(context) {
            let prm = {
                token: one_token(),
                group_id: 0
            }

            context.dispatch("system/postme", {
                url: "systm/user/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['users', d.records])
                }
            }, { root: true })
        },

        async transfer(context) {
            let ids = []
            for (let t of context.state.transfers)
                ids.push(t.M_CustomerID)

            let prm = {
                token: one_token(),
                old_uid: context.state.selected_user_from.user_id,
                new_uid: context.state.selected_user_to.user_id,
                type: context.state.selected_transfer_type.id,
                customer_ids: ids.join(",")
            }

            context.dispatch("system/postme", {
                url: "master/customer/transfer",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ['dialog_transfer', false])
                    context.dispatch("customer/search", {}, { root: true })
                }
            }, { root: true })
        }
    }
}
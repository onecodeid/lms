import * as api from "./api_saleslead.js?t=asd"
import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        save: false,

        dialog_convert: false,

        lead_name: '',
        lead_phone: '',
        lead_address: '',
        lead_postcode: '',

        cities: [],
        selected_city: null,
        search_city: '',
        loading_city: false,

        customers: [],
        selected_customer: null,
        search_customer: '',

        tabs: [{ id: 'NEW', text: 'CUSTOMER BARU' }, { id: 'EXISTING', text: 'EXISTING CUSTOMER' }],
        selected_tab: null
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

        set_cities(state, v) {
            state.cities = v
        },

        set_selected_city(state, v) {
            state.selected_city = v
        },

        set_customers(state, data) {
            state.customers = data
        },

        set_selected_customer(state, val) {
            state.selected_customer = val
        },

        set_selected_tab(state, val) {
            state.selected_tab = val
        }
    },

    actions: {
        async save(context) {
            context.commit('set_common', ['save', true])
            let prm = {
                lead_id: context.rootState.saleslead.selected_lead.lead_id,
                customer_id: context.state.selected_tab.id == 'NEW' ? 0 : context.state.selected_customer.M_CustomerID,
                customer_data: JSON.stringify({
                    name: context.state.lead_name,
                    address: context.rootState.saleslead_address.address,
                    phone: context.state.lead_phone,
                    postcode: context.rootState.saleslead_address.postcode,
                    kelurahan: context.rootState.saleslead_address.selected_village ?
                        context.rootState.saleslead_address.selected_village.M_KelurahanID : 0
                }),

                token: one_token()
            }

            context.dispatch("system/postme", {
                url: "sales/lead/convert",
                prm: prm,
                callback: function(d) {
                    context.commit('set_common', ['false', true])
                    context.commit('set_common', ['dialog_convert', false])
                    context.dispatch('saleslead/search', null, { root: true })
                    window.open(context.state.URL + "../ui/app/sales-order-seller-add/?lead=" + d.lead_id)
                },
                failback: function(e) {
                    context.commit('set_common', ['false', true])
                }
            }, { root: true })
        },

        async search_city(context) {
            context.commit('set_common', ['save', true])
            context.commit('set_common', ['loading_city', true])
            let prm = {
                token: one_token(),
                search: context.state.search_city
            }

            context.dispatch("system/postme", {
                url: "master/city/search_city_reverse",
                prm: prm,
                callback: function(d) {
                    context.commit("set_cities", d.records)
                    context.commit('set_common', ['loading_city', false])
                },
                failback: function(e) {
                    context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_customer(context) {
            let prm = {
                token: one_token(),
                search: context.state.search_customer
            }

            context.dispatch("system/postme", {
                url: "master/customer/search_autocomplete",
                prm: prm,
                callback: function(d) {
                    context.commit("set_customers", d.records)
                },
                failback: function(e) {}
            }, { root: true })

            // try {
            //     let prm = {
            //         token: one_token(),
            //         search: context.state.search_customer
            //     }

            //     let resp = await api.search_customer(prm)

            //     if (resp.status != "OK") {
            //         context.commit("update_search_status", 3)
            //         context.commit("update_search_error_message", resp.message)
            //     } else {
            //         context.commit("update_search_status", 2)
            //         context.commit("update_search_error_message", "")

            //         context.commit("set_customers", resp.data.records)
            //     }
            // } catch (e) {
            //     context.commit("update_search_status", 3)
            //     context.commit("update_search_error_message", e.message)
            // }
        }
    }


}
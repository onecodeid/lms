// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import { one_token, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        token: one_token(),
        search_status: 0,
        search_error_message: '',
        search: '',

        contacts: [],
        total_contact: 0,
        total_contact_page: 0,
        selected_contact: {},

        search_province_status: 0,
        provinces: [],
        selected_province: null,

        search_city_status: 0,
        cities: [],
        selected_city: null,

        search_tag_status: 0,
        tags: [],
        selected_tag: null,

        current_page: 1,
        report_url: '',

        dialog_transfer: true
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
        }

    },
    actions: {

        async search(context) {
            let prm = {
                token: one_token(),
                page: context.state.current_page,
                search: context.state.search,
                city: context.state.selected_city ? context.state.selected_city.M_CityID : 0,
                province: context.state.selected_province ? context.state.selected_province.M_ProvinceID : 0
            }

            context.dispatch("system/postme", {
                url: "crm/contact/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['contacts', d.records])
                }
            }, { root: true })
        },

        async search_province(context) {
            let prm = {
                token: one_token(),
                search: context.state.search_province
            }

            context.dispatch("system/postme", {
                url: "master/province/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['provinces', d.records])
                    context.commit("set_object", ["selected_province", []])

                    context.commit("set_object", ['cities', []])
                    context.commit("set_object", ["selected_city", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        // export async function search_items(prm) {
        //     return ajaxPost(URL + 'master/item/search_w_price', prm)
        // }

        // export async function search_packets(prm) {
        //     return ajaxPost(URL + 'master/packet/search_w_price', prm)
        // }

        async search_city(context) {
            let prm = {
                token: one_token(),
                search: context.state.search_city,
                province_id: context.state.selected_province[0].M_ProvinceID
            }

            context.dispatch("system/postme", {
                url: "master/city/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['cities', d.records])
                    context.commit("set_object", ["selected_city", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_tag(context) {
            let prm = {
                token: one_token(),
                search: '',
                page: 1,
                limit: 100
            }

            context.dispatch("system/postme", {
                url: "crm/tag/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_object", ['tags', d.records])
                    context.commit("set_object", ["selected_tag", []])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        }

        // async del(context, prm) {
        //     context.commit("update_search_status", 1)
        //     try {
        //         console.log(prm)
        //         prm.token = one_token()
        //         let resp = await api.del(prm)
        //         console.log(resp)
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
        // },

        // async search_province(context) {
        //     context.commit("set_common", ["search_province_status", 1])
        //     try {
        //         let prm = { token: one_token() }
        //         let resp = await api.search_province(prm)
        //         if (resp.status != "OK") {
        //             context.commit("set_common", ['search_province_status', 3])
        //             context.commit("update_search_error_message", resp.message)
        //         } else {
        //             context.commit("set_common", ["search_province_status", 2])
        //             context.commit("update_search_error_message", "")

        //             context.commit("set_provinces", resp.data.records)
        //         }
        //     } catch (e) {
        //         context.commit("set_common", ["search_province_status", 3])
        //         context.commit("update_search_error_message", e.message)
        //     }
        // },

        // async search_city(context) {
        //     context.commit("set_common", ["search_city_status", 1])
        //     try {
        //         let prm = { token: one_token(), search: context.state.search_city, province_id: context.state.selected_province.M_ProvinceID }
        //         let resp = await api.search_city(prm)
        //         if (resp.status != "OK") {
        //             context.commit("set_common", ["search_city_status", 3])
        //             context.commit("update_search_error_message", resp.message)
        //         } else {
        //             context.commit("set_common", ["search_city_status", 2])
        //             context.commit("update_search_error_message", "")

        //             context.commit("set_cities", resp.data.records)
        //         }
        //     } catch (e) {
        //         context.commit("set_common", ["search_city_status", 3])
        //         context.commit("update_search_error_message", e.message)
        //     }
        // }
    }
}
// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import { one_token, one_user, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],

        search: "",
        categories: [],
        selectedCategory: null
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
        }
    },
    actions: {
        async search_item({state, commit, dispatch}) {
            let prm = {
                token : one_token(),
                customer_level : 5,
                search : state.search,
                category : state.selectedCategory ? state.selectedCategory.M_CategoryID : 0
            }

            dispatch("system/postme", {
                url: "master/item/search_w_price",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['items', d.records])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_category({state, commit, dispatch}) {
            let prm = {
                search : ""
            }

            return dispatch("system/postme", {
                url: "master/category/search",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['categories', d.records])
                    return d
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        }
    }
}
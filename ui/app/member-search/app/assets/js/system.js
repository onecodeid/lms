// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_system.js"
import { URL, one_token, one_user } from "../../assets/js/global.js"

export default  {
    namespaced: true,
    state: {
        search : '',
        search_status: 0,
        search_error_message: "",
        menu_level_0: [],
        menu_level_1: [],
        menu_level_2: [],
        total_menu: 0,
        //sipe add breadcrumb dan hak akses
        bread_crumb : "",
        is_page_allowed : true,
        dashboard: "",
        user: one_user(),
        one_token: one_token(),

        menus : [],
        icons : [],
        drawer : false
    },

    mutations: {
        update_bread_crumb(state,val) {
            state.bread_crumb = val
        },
        update_dashboard(state,val) {
            state.dashboard= val
        },
        update_page_allowed(state,val) {
            state.is_page_allowed = val
        },
        update_search(state,val) {
            state.search=val 
        },
        update_search_error_message(state,status) {
            state.search_error_message = status
        },
        update_search_status(state,status) {
            state.search_status = status
        },

        update_menu_level_0 (state, data) {
            state.menu_level_0 = data
        },

        update_menu_level_1 (state, data) {
        state.menu_level_1 = data
        },

        update_menu_level_2 (state, data) {
            state.menu_level_2 = data
        },

        set_menus (state, v) {
            state.menus = v
        },

        set_icons (state, v) {
            state.icons = v
        },

        set_drawer (state, v) {
            state.drawer = v
        }
    },
    actions: {
        async search_menu_group(context) {
            context.commit("update_search_status",1)
            try {
                let resp= await api.search_menu_group({token:one_token()})
                if (resp.status != "OK") {
                context.commit("update_search_status",3)
                context.commit("update_search_error_message",resp.message)
                } else {
                context.commit("update_search_status",2)
                context.commit("update_search_error_message","")

                    context.commit('set_menus', resp.data[0])
                    context.commit('set_icons', resp.data[1])
                }
            } catch(e) {
                context.commit("update_search_status",3)
                context.commit("update_search_error_message", e.message )
            }
        },

        async logout () {
            try {
                var resp = await axios.post(URL + 'systm/user/logout', 
                { token: one_token() });
                if (resp.status != 200) {
                    return {
                        status: "ERR",
                        message: resp.statusText
                    };
                }
                let data = resp.data;
                return data;
            } catch (e) {
                return {
                    status: "ERR",
                    message: e.message
                };
            }
        },

        async do_logout(context) {  
            let x = async () => {
               try {
                  let resp = await context.dispatch('logout')
                  if (resp.status != "OK") {
                      alert('error')
                  } else {
                     window.localStorage.removeItem("token")
                     window.localStorage.removeItem("user")
                     location.replace('../system-login/')
                  }
               } catch (e) {
                  context.commit("update_login_status", 3)
                  context.commit("update_login_error_message", e.message)
               }
            }

            x()
        }
    }
}

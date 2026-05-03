import axios from "axios";
import { mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrInterpolate, ssrRenderClass, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  name: "AcceptInvitation",
  props: {
    token: {
      type: String,
      required: true
    },
    email: {
      type: String,
      required: true
    },
    seo: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      form: {
        name: "",
        password: "",
        password_confirmation: "",
        processing: false,
        errors: {}
      }
    };
  },
  methods: {
    async submitForm() {
      var _a;
      this.form.processing = true;
      this.form.errors = {};
      try {
        const response = await axios.post(`/accept-invitation/${this.token}`, {
          name: this.form.name,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation
        });
        window.location.href = response.data.redirect;
      } catch (error) {
        if (((_a = error.response) == null ? void 0 : _a.status) === 422) {
          this.form.errors = error.response.data.errors || {};
          if (error.response.data.message) {
            alert(error.response.data.message);
          }
        } else {
          alert("An error occurred. Please try again.");
        }
      } finally {
        this.form.processing = false;
      }
    }
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  _push(`<div${ssrRenderAttrs(mergeProps({ class: "container-fluid p-0" }, _attrs))}><div class="row min-vh-100 m-0"><div class="col-md-8 d-none d-md-block p-0"><img${ssrRenderAttr("src", $props.seo.hero_image)} alt="Hero Image" class="w-100 min-vh-100 object-fit-cover"></div><div class="col-md-4 d-flex bg-penda-primary align-items-center justify-content-center p-0"><div class="w-100 p-4"><h2 class="text-center text-white fw-bold mb-3"> Complete Your Registration </h2><p class="mt-2 text-center text-sm text-white mb-5"> Create your account for ${ssrInterpolate($props.email)}</p><form><div class="mb-3"><label for="name" class="form-label text-white"> Full Name </label><input id="name"${ssrRenderAttr("value", $data.form.name)} type="text" required class="${ssrRenderClass([{ "border-red-300": $data.form.errors.name }, "form-control"])}" placeholder="John Doe">`);
  if ($data.form.errors.name) {
    _push(`<p class="mt-2 text-sm text-red-600">${ssrInterpolate($data.form.errors.name[0])}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="mb-3"><label for="password" class="form-label text-white"> Password </label><input id="password"${ssrRenderAttr("value", $data.form.password)} type="password" required class="${ssrRenderClass([{ "border-red-300": $data.form.errors.password }, "form-control"])}" placeholder="••••••••">`);
  if ($data.form.errors.password) {
    _push(`<p class="mt-2 text-sm text-red-600">${ssrInterpolate($data.form.errors.password[0])}</p>`);
  } else {
    _push(`<!---->`);
  }
  _push(`</div><div class="mb-3"><label for="password_confirmation" class="form-label text-white"> Confirm Password </label><input id="password_confirmation"${ssrRenderAttr("value", $data.form.password_confirmation)} type="password" required class="form-control" placeholder="••••••••"></div><button type="submit"${ssrIncludeBooleanAttr($data.form.processing) ? " disabled" : ""} class="penda-btn penda-btn-secondary border w-100">${ssrInterpolate($data.form.processing ? "Creating Account..." : "Create Account")}</button></form></div></div></div></div>`);
}
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/AcceptInvitation.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AcceptInvitation = /* @__PURE__ */ _export_sfc(_sfc_main, [["ssrRender", _sfc_ssrRender]]);
export {
  AcceptInvitation as default
};

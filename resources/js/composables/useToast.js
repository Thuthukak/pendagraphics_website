import { useToast } from 'vue-toastification'

export function useNotify() {
    const toast = useToast()

    return {
        success: (msg) => toast.success(msg),
        error:   (msg) => toast.error(msg),
        info:    (msg) => toast.info(msg),
        warning: (msg) => toast.warning(msg),
    }
}
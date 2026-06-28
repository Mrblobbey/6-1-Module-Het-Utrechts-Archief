import { supabase, supabaseEnabled } from './useSupabase'

const BUCKET = 'panorama'

export function useImageUpload() {
  async function upload(file) {
    if (!supabaseEnabled) {
      const dataUrl = await new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.onload = () => resolve(reader.result)
        reader.onerror = reject
        reader.readAsDataURL(file)
      })
      return { success: true, url: dataUrl, fileName: file.name }
    }

    const path = `${Date.now()}_${file.name}`
    const { error: uploadError } = await supabase.storage.from(BUCKET).upload(path, file)
    if (uploadError) return { success: false, error: uploadError.message }
    const { data } = supabase.storage.from(BUCKET).getPublicUrl(path)
    return { success: true, url: data.publicUrl, fileName: path }
  }

  return { upload }
}

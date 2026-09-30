const readCookie = (name: string): string | null => {
  const match = document.cookie
    .split('; ')
    .find((row) => row.startsWith(`${name}=`))

  if (!match) {
    return null
  }

  return decodeURIComponent(match.split('=').slice(1).join('='))
}

export const ensureCsrfCookie = async (): Promise<void> => {
  const response = await fetch('/sanctum/csrf-cookie', {
    method: 'GET',
    credentials: 'include',
    headers: {
      Accept: 'application/json',
    },
  })

  if (!response.ok && response.status !== 204) {
    throw new Error(`CSRF inicializācija neizdevās (${response.status})`)
  }
}

export const apiFetch = async <T,>(
  path: string,
  init: RequestInit = {},
): Promise<T> => {
  const headers = new Headers(init.headers)
  headers.set('Accept', 'application/json')

  if (init.body && !headers.has('Content-Type')) {
    headers.set('Content-Type', 'application/json')
  }

  const xsrf = readCookie('XSRF-TOKEN')
  if (xsrf) {
    headers.set('X-XSRF-TOKEN', xsrf)
  }

  const response = await fetch(path, {
    ...init,
    credentials: 'include',
    headers,
  })

  if (response.status === 204) {
    return undefined as T
  }

  const payload = await response.json().catch(() => null)

  if (!response.ok) {
    const message =
      payload?.message ??
      payload?.errors?.email?.[0] ??
      `Pieprasījums neizdevās (${response.status})`
    throw new Error(message)
  }

  return payload as T
}

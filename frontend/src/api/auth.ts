import type { User } from '../types/document'
import { apiFetch, ensureCsrfCookie } from './client'

type AuthResponse = {
  user: User
}

export const fetchMe = async (): Promise<User> => {
  const payload = await apiFetch<AuthResponse>('/api/me')
  return payload.user
}

export const login = async (email: string, password: string): Promise<User> => {
  await ensureCsrfCookie()
  const payload = await apiFetch<AuthResponse>('/api/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  })
  return payload.user
}

export const logout = async (): Promise<void> => {
  await ensureCsrfCookie()
  await apiFetch<void>('/api/logout', {
    method: 'POST',
  })
}

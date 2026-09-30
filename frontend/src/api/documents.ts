import type { DocumentFilters, DocumentsResponse } from '../types/document'
import { apiFetch } from './client'

export const fetchDocuments = async (
  filters: DocumentFilters,
): Promise<DocumentsResponse> => {
  const params = new URLSearchParams()

  if (filters.search.trim()) {
    params.set('search', filters.search.trim())
  }
  if (filters.importance) {
    params.set('importance', filters.importance)
  }
  if (filters.category) {
    params.set('category', filters.category)
  }
  if (filters.is_active !== '') {
    params.set('is_active', filters.is_active)
  }
  if (filters.sort) {
    params.set('sort', filters.sort)
  }
  if (filters.direction) {
    params.set('direction', filters.direction)
  }

  const query = params.toString()
  const path = query ? `/api/documents?${query}` : '/api/documents'

  return apiFetch<DocumentsResponse>(path)
}

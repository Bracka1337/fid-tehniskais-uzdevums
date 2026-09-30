import type { Category, Importance } from '../types/document'

export const importanceLabels: Record<Importance, string> = {
  low: 'Zems',
  medium: 'Vidējs',
  high: 'Augsts',
  critical: 'Kritisks',
}

export const categoryLabels: Record<Category, string> = {
  public: 'Publisks',
  internal: 'Iekšējs',
  restricted: 'Ierobežotas pieejamības',
  confidential: 'Konfidenciāls',
}

import Helper from '@/shared/utils/helper';
import { expect, test } from 'vitest';

test('formatDate formats a date correctly', () => {
  const date = new Date('2023-01-01');
  const formattedDate = Helper.formatDate(date);
  const expectedFormattedDate = '01/01/2023';

  expect(formattedDate).toBe(expectedFormattedDate);
});


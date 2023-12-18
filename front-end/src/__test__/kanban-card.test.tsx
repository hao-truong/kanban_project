import KanbanCard from '@/application/board/KanbanCard';
import { render, screen, fireEvent } from '@testing-library/react';
import { vi } from 'vitest';
import { QueryClient, QueryClientProvider } from 'react-query';
import { any } from 'prop-types';

const queryClient = new QueryClient();

const mockSetIsDraggingCard = vi.fn();
const mockCard: Card = {
  id: 59,
  column_id: 68,
  title: 'card 6',
  description: '',
  created_at: new Date('2023-12-11 22:28:33.019'),
  updated_at: new Date('2023-12-11 22:28:33.019'),
  assigned_user: null,
  position: 1,
};
const mockBoardId = 1;

describe('KanbanCard render', () => {
  it('should call handleDragStart with the correct arguments', () => {
    // const mockSetCardNeedDrop = vi.fn();
    const mockHandleDragStart = vi.fn((e: any, card: Card) => {
      console.log('handleDragStart arguments:', e, card);
    });

    const { container: kanbanCard } = render(
      <QueryClientProvider client={queryClient}>
        <KanbanCard
          card={mockCard}
          boardId={mockBoardId}
          setIsDraggingCard={mockSetIsDraggingCard}
        />
        ,
      </QueryClientProvider>,
    );

    const dragEvent = new Event('dragstart', { bubbles: true });
    Object.defineProperty(dragEvent, 'dataTransfer', {
      value: { setData: vi.fn() },
    });
    kanbanCard.dispatchEvent(dragEvent);

    // Assert that setCardNeedDrop was called with the correct arguments
    expect(mockHandleDragStart).toHaveBeenCalledWith('abcs', mockCard);
    console.log('mockHandleDragStart calls:', mockHandleDragStart.mock.calls);
  });
});

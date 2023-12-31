import { create } from 'zustand';

interface GlobalState {
  user: User | null;
  setUser: (data: User) => void;
  columnNeedDrop: Column | null;
  setColumnNeedDrop: (data: Column | null) => void;
  cardNeedDrop: Card | null;
  setCardNeedDrop: (data: Card | null) => void;
  targetColumnDrop: Column | null;
  setTargetColumnDrop: (data: Column | null) => void;
}

export const useGlobalState = create<GlobalState>((set) => ({
  user: null,
  setUser: (data) => set((state) => ({ ...state, user: data })),
  columnNeedDrop: null,
  setColumnNeedDrop: (data) => set((state) => ({ ...state, columnNeedDrop: data })),
  targetColumnDrop: null,
  setTargetColumnDrop: (data) => set((state) => ({ ...state, targetColumnDrop: data })),
  cardNeedDrop: null,
  setCardNeedDrop: (data) => set((state) => ({ ...state, cardNeedDrop: data })),
}));

import { create } from 'zustand';

interface GlobalState {
  user: User | null;
  setUser: (data: User) => void;
}

export const useGlobalState = create<GlobalState>((set) => ({
  user: null,
  setUser: (data) => set((state) => ({ ...state, user: data })),
}));

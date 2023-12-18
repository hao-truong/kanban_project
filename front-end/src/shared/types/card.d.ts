type Card = {
  id: number;
  position: number;
  assigned_user: User | null;
  description: string;
  created_at: Date;
  updated_at: Date;
  column_id: number;
  title: string;
};

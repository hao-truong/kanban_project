import CardService from '@/shared/services/CardService';
import { useGlobalState } from '@/shared/storages/GlobalStorage';
import { useQueryClient } from 'react-query';
import { toast } from 'react-toastify';

interface itemProps {
  column: Column;
  setIsShow: Function;
}

const EmptyDrop = ({ column, setIsShow }: itemProps) => {
  const queryClient = useQueryClient();
  const { cardNeedDrop, setCardNeedDrop } = useGlobalState();
  const handleDrop = async () => {
    if (!cardNeedDrop) {
      return;
    }

    await CardService.changeColumnForCard(
      {
        cardId: cardNeedDrop.id,
        columnId: cardNeedDrop.column_id,
        boardId: column.board_id,
      },
      {
        destinationColumnId: column.id,
      },
    )
      .then(() => {
        queryClient.invalidateQueries(`getCards${column.id}`);
        queryClient.invalidateQueries(`getCards${cardNeedDrop.column_id}`);
        setCardNeedDrop(null);
      })
      .catch((responseError: ResponseError) => toast.error(responseError.message));

    setIsShow(false);
  };

  return (
    <div
      className="h-[130px] bg-red-600/[.4] rounded-md text-center flex items-center justify-center"
      draggable
      onDrop={handleDrop}
    >
      Drop here
    </div>
  );
};

export default EmptyDrop;

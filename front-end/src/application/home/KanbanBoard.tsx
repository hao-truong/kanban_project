import Helper from "@/shared/utils/helper";

interface itemProps {
    board: Board;
}

const KanbanBoard = ({board}: itemProps) => {
    return (
        <div className="bg-slate-200 max-w-[400px] p-4 text-center flex flex-col gap-5 rounded-xl">
            <h2 className="uppercase text-xl font-bold">{board.title}</h2>
            <div className="flex flex-row justify-between"><strong className="text-red-700">Created At:</strong> <span>{Helper.formatDate(board.created_at)}</span></div>
            <div className="flex flex-row justify-between"><strong className="text-red-700">Updated At:</strong> <span>{Helper.formatDate(board.updated_at)}</span></div>
        </div>
    )
}

export default KanbanBoard;
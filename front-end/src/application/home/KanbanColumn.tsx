import { Plus } from "lucide-react";

const KanbanColumn = () => {
    return (
        <div className="bg-slate-200 p-4 min-w-[300px] rounded-lg">
            <h2 className="uppercase pb-4">TO DO</h2>
            <button className="w-full flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400">
                <Plus />
                <span>Create card</span>
            </button>
        </div>
    )
}

export default KanbanColumn;
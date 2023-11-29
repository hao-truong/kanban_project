import { Plus } from "lucide-react";
import KanbanColumn from "./KanbanColumn";

const KanbanBoard = () => {
    return (
        <div>
            <div className="flex flex-row justify-between my-10">
                <h2 className="uppercase">KANBAN BOARD TEST</h2>
                <button className="flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400">
                    <Plus />
                    <span>Create column</span>
                </button>
            </div>
            <div className="flex flex-row gap-4 overflow-auto">
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
                <KanbanColumn />
            </div>
        </div>

    )
}

export default KanbanBoard;
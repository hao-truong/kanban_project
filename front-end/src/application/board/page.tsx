import { Plus } from "lucide-react";
import KanbanColumn from "../home/KanbanColumn";

const BoardPage = () => {

    const createColumn = async () => {
        console.log("create new column");
    };

    return (
        <div>
            <div className="flex flex-row justify-between my-10">
                <h2 className="uppercase">KANBAN BOARD TEST</h2>
                <button className="flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400" onClick={createColumn}>
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

export default BoardPage;
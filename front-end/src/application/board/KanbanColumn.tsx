import { MAX_LENGTH_INPUT_STRING, MAX_TITLE_LENGTH, MIN_LENGTH_INPUT_STRING } from "@/shared/utils/constant";
import { yupResolver } from "@hookform/resolvers/yup";
import { Check, MoreHorizontal, Plus, X } from "lucide-react";
import { SubmitHandler, useForm } from "react-hook-form";
import { toast } from "react-toastify";
import * as yup from "yup";
// import { useQueryClient } from 'react-query';
import Helper from "@/shared/utils/helper";
import { useEffect, useRef, useState } from "react";
import { OutlinedInput } from "@mui/material";
import { useGlobalState } from "@/shared/storages/GlobalStorage";

interface itemProps {
    column: Column;
}

const schemaValidation = yup
    .object({
        title: yup.string().required("Title is required!")
            .min(
                MIN_LENGTH_INPUT_STRING,
                `Title must be at least ${MIN_LENGTH_INPUT_STRING} characters long`
            )
            .max(
                MAX_LENGTH_INPUT_STRING,
                `Title must be at least ${MAX_LENGTH_INPUT_STRING} characters long`
            ),
    })
    .required();


const KanbanColumn = ({ column }: itemProps) => {
    const { user } = useGlobalState();
    // const queryClient = useQueryClient();
    const titleRef = useRef<HTMLFormElement | null>(null);
    const [isClickTilte, setIsClickTitle] = useState<boolean>(false);
    const [isShowMenu, setIsShowMenu] = useState<boolean>(false);
    const [isHoverTitle, setIsHoverTitle] = useState<boolean>(false);
    const [isCreator, setIsCreator] = useState<boolean>(false);
    const {
        register,
        handleSubmit,
        formState: { errors },
        setValue
    } = useForm<UpdateColumnReq>({
        resolver: yupResolver(schemaValidation),
    });
    const onSubmit: SubmitHandler<UpdateColumnReq> = async (reqData) => {
        try {
            if (reqData.title === column.title) {
                setIsClickTitle(false);
                return;
            }


        } catch (error: any) {
            toast.error(error.message);
        }
    };

    useEffect(() => {
        setIsCreator(column.creator_id === user?.id);
    }, [user])

    useEffect(() => {
        if (!isClickTilte) {
            setValue('title', column.title);
        }

        if (!isHoverTitle) {
            setIsShowMenu(false);
        }
    }, [isClickTilte, isHoverTitle])

    return (
        <div className="bg-slate-200 p-4 text-center flex flex-col gap-5 rounded-xl min-w-[300px] min-h-[300px]">
            <div
                onMouseOver={() => setIsHoverTitle(true)}
                onMouseLeave={() => setIsHoverTitle(false)}
            >
                {
                    isClickTilte &&
                    <form ref={titleRef} className="relative" onSubmit={handleSubmit(onSubmit)}>
                        <OutlinedInput
                            autoFocus
                            className="w-full bg-white"
                            onFocus={() => Helper.handleOutSideClick(titleRef, setIsClickTitle)}
                            id="outlined-adornment-weight"
                            aria-describedby="outlined-weight-helper-text"
                            {...register('title')}
                            error={errors.title ? true : false}
                            inputProps={{ maxLength: MAX_TITLE_LENGTH }}
                        />
                        <div className="flex flex-row justify-end gap-2 mt-2 absolute right-0">
                            <button type="submit">
                                <Check size={40} className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer" />
                            </button>
                            <X size={40} className="p-2 bg-white hover:bg-slate-100 rounded-sm shadow-lg cursor-pointer" onClick={() => setIsClickTitle(false)} />
                        </div>
                    </form>
                }
                {
                    !isClickTilte &&
                    <div className="flex flex-row justify-between items-center gap-4">
                        <h2 className="w-full uppercase text-xl font-bold py-2 hover:bg-white cursor-pointer text-left"
                            onClick={() => {
                                if (isCreator) {
                                    setIsClickTitle(true);
                                }
                            }}>
                            {column.title}
                        </h2>
                        {
                            isHoverTitle &&
                            (
                                <div className="relative">
                                    <MoreHorizontal className="cursor-pointer hover:bg-slate-100 p-2" size={40} onClick={() => setIsShowMenu(!isShowMenu)} />
                                    {
                                        isShowMenu &&
                                        <ul className="absolute bg-white top-full right-0 py-1 w-max">
                                            {
                                                isCreator && (
                                                    <div>
                                                        <li className="py-1 px-4 text-left hover:bg-slate-100 cursor-pointer" >Delete</li>
                                                    </div>
                                                )
                                            }
                                        </ul>
                                    }
                                </div>
                            )
                        }
                    </div>
                }
            </div>
            <button className="w-full flex flex-row items-center gap-2 px-4 py-2 hover:bg-slate-400">
                <Plus />
                <span>Create card</span>
            </button>
        </div>
    )
}

export default KanbanColumn;
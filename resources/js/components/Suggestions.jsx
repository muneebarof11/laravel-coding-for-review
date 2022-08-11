import React, {useEffect, useState, StrictMode } from 'react';
import socialService from "../services/social.service";
import {Loader} from "./Loader";
import {toast} from 'react-hot-toast'

export const Suggestions = (props) => {
    const [suggestions, setSuggestions] = useState({data: []});
    const [loading, setLoading] = useState(true);
    const [btnLoading, setBtnLoading] = useState([]);

    const fetchData = async () => {
        const response = await socialService.suggestions();
        setSuggestions(() => {
            return response.data
        });

        setBtnLoading((previous) => {
            const filled = Array.from({length: response.data.data.length}, () => false)
            return filled;
        });

        setLoading(false);
    }

    const connectWithUser = async (email, index) => {
        try {
            toggleBtnLoading(index)

            const response = await socialService.connectWithUser({recipient: email});
            toast(response.data.message)

            toggleBtnLoading(index)
            setLoading(true);

            props.updateStats()
            fetchData();


        } catch (error) {
            setBtnLoading(false);

            // worst way to display error
            toast.error(error.response.data.message);
        }
    }

    const toggleBtnLoading = (index) => {
        setBtnLoading((previous) => {
            let loadings = [...previous];
            loadings[index] = !previous[index];
            return loadings
        });
    }

    useEffect(() => {
        fetchData();
    }, [])

    return (
        loading ? <Loader /> : suggestions.data.length > 0 ? suggestions.data.map((item, index) => {
            return <div className="my-2 shadow  text-white bg-dark p-1" id="" key={index}>
                <div className="d-flex justify-content-between">
                    <table className="ms-1">
                        <tr>
                            <td className="align-middle">{item.name}</td>
                            <td className="align-middle">-</td>
                            <td className="align-middle">{item.email}</td>
                            <td className="align-middle">&nbsp;</td>
                        </tr>
                    </table>
                    <div>
                        <button disabled={btnLoading[index]} id="create_request_btn_" className="btn btn-primary me-1" onClick={() => connectWithUser(item.email, index)}>
                            {btnLoading[index] ? 'Connecting...' : 'Connect' }
                        </button>
                    </div>
                </div>
            </div>
        }) : <>N/A</>
    )
}

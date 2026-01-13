import React, { useState } from 'react';
import axios from 'axios';

export default function Index({ movies: initialMovies }) {
    const [movies, setMovies] = useState(initialMovies || []);
    const [editingMovie, setEditingMovie] = useState(null);
    const [formData, setFormData] = useState({ name: '', release_date: '', length: '', description: '' });

    // Modal megnyitása
    const openEditModal = (movie) => {
        setEditingMovie(movie);
        setFormData({
            name: movie.name,
            release_date: movie.release_date,
            length: movie.length,
            description: movie.description
        });
    };

    // Modal bezárása
    const closeModal = () => setEditingMovie(null);

    // Film törlése
    const handleDelete = async (id) => {
        if (!confirm("Biztosan törölni szeretnéd a filmet?")) return;

        try {
            await axios.delete(`http://localhost:8001/api/movies/${id}`);
            setMovies(prev => prev.filter(movie => movie.id !== id));
            alert("Film törölve!");
        } catch (error) {
            console.error(error);
            alert("Hiba történt a törlés során!");
        }
    };

    // Film mentése modalból
    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.put(`http://localhost:8001/api/movies/${editingMovie.id}`, formData);
            setMovies(prev => prev.map(m => m.id === editingMovie.id ? response.data : m));
            closeModal();
            alert("Film frissítve!");
        } catch (error) {
            console.error(error);
            alert("Hiba történt a frissítés során!");
        }
    };

    return (
        <div className="max-w-5xl mx-auto p-4">
            <h1 className="text-3xl font-bold mb-6">Filmek</h1>

            {movies.length > 0 ? (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    {movies.map(movie => (
                        <div key={movie.id} className="bg-white shadow rounded p-4 hover:shadow-lg transition">
                            <img src={movie.pic_path} alt={movie.name} className="w-full h-48 object-cover rounded mb-3"/>
                            <h2 className="text-xl font-semibold">{movie.name}</h2>
                            <p className="text-gray-500">{movie.release_date} | {movie.length}</p>
                            <p className="mt-2 text-gray-700">{movie.description}</p>

                            {/* Gombok */}
                            <div className="mt-4 flex gap-2">
                                <button
                                    style={{ backgroundColor: 'rgba(135, 176, 192, 1)', color: 'white' }}
                                    className="px-4 py-2 rounded"
                                    onClick={() => openEditModal(movie)}
                                >
                                    Szerkesztés
                                </button>

                                <button
                                    style={{ backgroundColor: 'rgba(37, 55, 94, 1)', color: 'white' }}
                                    className="px-4 py-2 rounded"
                                    onClick={() => handleDelete(movie.id)}
                                >
                                    Törlés
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-500">Nincsenek filmek.</p>
            )}

            {/* Modal */}
            {editingMovie && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                    <div className="bg-white p-6 rounded shadow-lg w-96">
                        <h2 className="text-xl font-bold mb-4">Film szerkesztése</h2>
                        <form onSubmit={handleSubmit} className="flex flex-col gap-2">
                            <input
                                type="text"
                                value={formData.name}
                                onChange={e => setFormData({...formData, name: e.target.value})}
                                placeholder="Film neve"
                                className="border px-2 py-1 rounded"
                            />
                            <input
                                type="text"
                                value={formData.release_date}
                                onChange={e => setFormData({...formData, release_date: e.target.value})}
                                placeholder="Megjelenés éve"
                                className="border px-2 py-1 rounded"
                            />
                            <input
                                type="text"
                                value={formData.length}
                                onChange={e => setFormData({...formData, length: e.target.value})}
                                placeholder="Hossz"
                                className="border px-2 py-1 rounded"
                            />
                            <textarea
                                value={formData.description}
                                onChange={e => setFormData({...formData, description: e.target.value})}
                                placeholder="Leírás"
                                className="border px-2 py-1 rounded"
                            />
                            <div className="flex justify-end gap-2 mt-2">
                                <button type="button" onClick={closeModal} className="px-4 py-2 rounded bg-gray-500 text-white">Mégse</button>
                                <button type="submit" className="px-4 py-2 rounded bg-blue-500 text-white">Mentés</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

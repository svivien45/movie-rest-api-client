export default function Index({ movies }) {
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
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-500">Nincsenek filmek.</p>
            )}
        </div>
    );
}




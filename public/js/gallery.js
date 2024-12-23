document.addEventListener("DOMContentLoaded", function () {
  const galleryForm = document.getElementById("galleryForm");
  const titleInput = document.getElementById("title");
  const descriptionInput = document.getElementById("description");
  const imageUrlInput = document.getElementById("image_url");
  const submitButton = galleryForm.querySelector("button[type='submit']");
  const galleryTable = document
    .getElementById("galleryTable")
    .querySelector("tbody");

  let editMode = false;
  let editId = null;

  // Validasi form
  function validateForm() {
    const isValid = titleInput.value.trim() && imageUrlInput.value.trim();
    submitButton.disabled = !isValid;
  }

  // Tambahkan baris galeri ke tabel
  function addGalleryRow(gallery) {
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${gallery.title}</td>
        <td>${gallery.description || "Tidak ada deskripsi"}</td>
        <td><img src="${gallery.image_url}" alt="${
      gallery.title
    }" style="max-width: 100px;" /></td>
        <td>
          <button class="edit-btn btn-green" data-id="${
            gallery.id
          }">Edit</button>
          <button class="delete-btn btn-red" data-id="${
            gallery.id
          }">Hapus</button>
        </td>
      `;

    galleryTable.appendChild(row);
    attachRowEventListeners(row);
  }

  // Reset tabel
  function resetTable() {
    galleryTable.innerHTML = "";
  }

  // Event listener pada tombol Edit dan Hapus
  function attachRowEventListeners(row) {
    const editBtn = row.querySelector(".edit-btn");
    const deleteBtn = row.querySelector(".delete-btn");

    editBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      fetch(`../controllers/gallery_controller.php?action=fetchById&id=${id}`)
        .then((response) => response.json())
        .then((data) => {
          if (data) {
            titleInput.value = data.title;
            descriptionInput.value = data.description;
            imageUrlInput.value = data.image_url;

            editMode = true;
            editId = id;
            submitButton.textContent = "Update Gambar";
          } else {
            alert("Gambar tidak ditemukan.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Gagal mengambil data gambar.");
        });
    });

    deleteBtn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      if (confirm("Yakin ingin menghapus gambar ini?")) {
        fetch(`../controllers/gallery_controller.php?action=delete`, {
          method: "POST",
          body: new URLSearchParams({ id }),
        })
          .then((response) => response.json())
          .then((data) => {
            alert(data.message || data.error);
            resetTable();
            loadGallery();
          })
          .catch((error) => {
            console.error("Error:", error);
            alert("Gagal menghapus gambar.");
          });
      }
    });
  }

  // Submit form
  galleryForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new URLSearchParams({
      title: titleInput.value.trim(),
      description: descriptionInput.value.trim(),
      image_url: imageUrlInput.value.trim(),
    });

    const action = editMode ? "update" : "create";
    if (editMode) {
      formData.append("id", editId);
    }

    fetch(`../controllers/gallery_controller.php?action=${action}`, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        alert(data.message || data.error);
        resetTable();
        loadGallery();
        galleryForm.reset();
        submitButton.textContent = "Unggah Gambar";
        editMode = false;
      })
      .catch((error) => {
        console.error("Error:", error);
        alert(
          `Terjadi kesalahan saat ${
            editMode ? "memperbarui" : "menambah"
          } gambar.`
        );
      });
  });

  // Muat data galeri dari server
  function loadGallery() {
    fetch("../controllers/gallery_controller.php?action=fetch", {
      method: "GET",
    })
      .then((response) => response.json())
      .then((gallery) => {
        resetTable();
        gallery.forEach((item) => addGalleryRow(item));
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Gagal memuat data galeri.");
      });
  }

  // Inisialisasi
  validateForm();
  loadGallery();

  titleInput.addEventListener("input", validateForm);
  descriptionInput.addEventListener("input", validateForm);
  imageUrlInput.addEventListener("input", validateForm);
});

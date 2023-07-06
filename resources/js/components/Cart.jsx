import React, { Component } from "react";
import ReactDOM from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";

class Cart extends Component {
    
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            capitalValue: 0,
            cashIn: 0,
            cashlessIn: 0,
            pendapatan: 0,
            barcode: "",
            search: "",
            customer_id: "",
            payment_method: "Cash",
            showModal: false,
            selectedProduct: null,
            minimumLowValue: 0,
            orderId: null,
        };
        this.setPaymentMethod = this.setPaymentMethod.bind(this);

        this.handleOpenModal = this.handleOpenModal.bind(this);
        this.loadCashData = this.loadCashData.bind(this);
        this.postStock = this.postStock.bind(this),
        this.handleCloseModal = this.handleCloseModal.bind(this);
        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);

        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
    }

    componentDidMount() {
        // load user cart
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
        this.loadCashData();
    }

    loadCashData() {
        axios.get('/cart').then((res) => {
          const cartData = res.data;
            this.setState({
              cartData
            });
          });
      }

    loadCustomers() {
        axios.get(`/customers`).then((res) => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `?search=${search}` : "";
        axios.get(`/products${query}`).then((res) => {
            const products = res.data.data;
            const activeProducts = products.filter((product) => product.status === 1);
            this.setState({ products: activeProducts });
        });
    }

    loadProduk(search = "") {
        const que = !!search ? `?search=${search}` : "";
        axios.get(`/products${que}`).then((res) => {
            const produk = res.data.data.map((product) => {
                return {
                    ...product,
                    low_price: product.low_price, // tambahkan properti low_price
                };
            });
            this.setState({ produk });
        });
    }

    loadMin(search = "") {
        const que = !!search ? `?search=${search}` : "";
        axios.get(`/products${que}`).then((res) => {
            const min = res.data.data.map((product) => {
                return {
                    ...product,
                    minimum_low: product.minimum_low, // tambahkan properti low_price
                    minimumLowValue: minimum_low,
                };
            });
            this.setState({ min });
        });
    }

    handleSubmit = (id) => (event) => {
        event.preventDefault();
        const nilaiInput = event.target.querySelector('input[name="inputJumlah"]').value;
        let barang = this.state.cart.find((c) => c.id === id);
        // Gunakan nilaiInput dan id sesuai kebutuhan
        this.handleChangeQty(barang.id,nilaiInput);
        this.handleCloseProduct();
      };

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        console.log(barcode);
        this.setState({ barcode });
    }

    loadCart() {
        axios.get("/cart").then((res) => {
            const cart = res.data;
            this.setState({ cart });
        });
    }

    handleScanBarcode(event) {
        event.preventDefault();
        const { barcode } = this.state;
        if (!!barcode) {
            axios
                .post("/cart", { barcode })
                .then((res) => {
                    this.loadCart();
                    this.setState({ barcode: "" });
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }
    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
            }
            return c;
        });

        this.setState({ cart });
        if (!qty) return;

        axios
            .post("/cart/change-qty", { product_id, quantity: qty })
            .then((res) => {})
            .catch((err) => {
                Swal.fire("Error!", err.response.data.message, "error");
            });
    }

    getTotal(cart) {
        const total = cart.map((c) => {
            if (c.pivot.quantity >= c.minimum_low) {
                return c.pivot.quantity * c.low_price;
            } else {
                return c.pivot.quantity * c.price;
            }
        });
    
        return sum(total).toFixed(2);
    }

    handleOpenModal = () => {
        this.setState({ showModal: true, });
      };

    handleCloseModal = () => {
        this.setState({ showModal: false, });
      };
    
    handleOpenProduct = (barcode) => {
        const selectedProduct = this.state.products.find((p)=>p.barcode===barcode);
        this.addProductToCart(barcode);
        console.log(selectedProduct);
        if(!!selectedProduct){
            this.setState({selectedProduct,showModal: true});
        }
    };
    handleCloseProduct = () => {
        this.setState({ selectedProduct: null, showModal: false });
    };

    handleClickDelete(product_id) {
        axios
            .post("/cart/delete", { product_id, _method: "DELETE" })
            .then((res) => {
                const cart = this.state.cart.filter((c) => c.id !== product_id);
                this.setState({ cart });
            });
    }
    handleEmptyCart() {
        axios.post("/cart/empty", { _method: "DELETE" }).then((res) => {
            this.setState({ cart: [] });
        });
    }
    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }
    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(barcode) {
        let product = this.state.products.find((p) => p.barcode === barcode);
        if (!!product) {
            // if product is already in cart
            let cart = this.state.cart.find((c) => c.id === product.id);
            if (!!cart) {
                // update quantity
                this.setState({
                    cart: this.state.cart.map((c) => {
                        if (
                            c.id === product.id &&
                            product.quantity > c.pivot.quantity
                        ) {
                            c.pivot.quantity = c.pivot.quantity + 1;
                        }
                        return c;
                    }),
                });
            } else {
                if (product.quantity > 0) {
                    product = {
                        ...product,
                        pivot: {
                            quantity: 1,
                            product_id: product.id,
                            user_id: 1,
                        },
                    };
    
                    this.setState({ cart: [...this.state.cart, product] });
                }
            }
    
            axios
                .post("/cart", { barcode })
                .then((res) => {
                    this.loadCart();
                    console.log(res);
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }
    
    

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }
    
    setPaymentMethod(event) {
        const paymentMethod = event.target.value;
        this.setState({ payment_method: paymentMethod });
    }
    
      postStock() {
        const { cart, products } = this.state;
        const requests = [];
      
        cart.forEach((c) => {
          const product = products.find((p) => p.id === c.id);
          const qtynow = product.quantity;
          const qtychange = qtynow - c.pivot.quantity;
      
          const requestData = {
            id: product.id,
            name: product.name,
            description: product.description,
            barcode: product.barcode,
            price: product.price,
            quantity: qtychange,
            status: product.status,
            category_product: 1,
            minimum_low: 0,
            brand: c.brand,
            low_price: c.low_price,
            stock_price: c.stock_price,
          };
      
          const url = `/products/history/${requestData.id}`;
          const request = axios.post(url, requestData);
          requests.push(request);
        });
      
        return Promise.all(requests);
      }

    handlePrintModal(orderId){
        Swal.fire({
            title: "Cetak nota?",
            showCancelButton: true,
            cancelButtonText: "Batal",
            confirmButtonText: "Cetak",
            showLoaderOnConfirm: true,
        }).then((result) => {
            console.log(orderId);
            if(result.isConfirmed){
                const printUrl = `/orders/${orderId}/print`;
                axios.get(printUrl)
                .then((res) => {
                    Swal.fire('Success', 'Nota tercetak.','success');
                }).catch((err) => {
                    Swal.fire('Error', 'Nota gagal dicetak.','error');
                })
            }
        });
    }

    handleClickSubmit() {
        const { customer_id, cart, payment_method } = this.state;
        const totalAmount = this.getTotal(this.state.cart);
      
        Swal.fire({
          title: "Uang Diterima",
          input: "text",
          inputValue: totalAmount,
          showCancelButton: true,
          cancelButtonText: "Batal",
          confirmButtonText: "Simpan",
          showLoaderOnConfirm: true,
          preConfirm: (amount) => {
            const requestData = {
              customer_id,
              items: cart.map((c) => {
                const price = c.pivot.quantity >= c.minimum_low ? c.low_price : c.price;
                const itemAmount = (c.pivot.quantity * price).toFixed(2);
                
                return {
                  product_id: c.id,
                  quantity: c.pivot.quantity,
                  payment_method,
                  amount: itemAmount,
                };
              }),
              amount: amount,
            };
            
            return axios
              .post("/orders", requestData)
              .then((res) => {
                const orderId = res.data.order_id;
                console.log("orderid", orderId);
                console.log("Response Data:", res.data);
                this.loadCart();
                this.loadProducts();
                this.postStock();

                Swal.fire({
                    title: "Cetak nota?",
                    showCancelButton: true,
                    cancelButtonText: "Batal",
                    confirmButtonText: "Cetak",
                }).then((result) => {
                    /*console.log(orderId);
                    if(result.isConfirmed){
                        const printUrl = `/orders/${orderId}/print`;
                        axios.get(printUrl)
                        .then((res) => {
                            Swal.fire('Success', 'Nota tercetak.','success');
                        }).catch((err) => {
                            Swal.fire('Error', 'Nota gagal dicetak.','error');
                        })
                    }*/
                });

                return res.data;
              })
              .catch((err) => {
                Swal.showValidationMessage(err.response.data.message);
              });
          },
          allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
          if (result.value) {
            Swal.fire({
                title: "Transaksi tersimpan",
                icon: 'success',
                timer: 800,
                showCancelButton: false,
                showConfirmButton: false
            })
          }
        });
      }
    
    render() {
        const { cart, products, customers, barcode,showModal,selectedProduct, capitalValue, cashIn, cashlessIn, pendapatan,cartData } = this.state;
        const totalAmount = this.getTotal(cart);
        return (
            
            <div className="row">
                <div className="col-md-6 col-lg-4">
                    <div className="row mb-2">
                        <div className="col">
                            <form onSubmit={this.handleScanBarcode}>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Scan Barcode..."
                                    value={barcode}
                                    onChange={this.handleOnChangeBarcode}
                                />
                            </form>
                        </div>
                        <div className="col">
                            <select
                                className="form-control"
                                onChange={this.setPaymentMethod}
                                defaultValue="Cash"
                            >
                                <option value="">Pilih metode pembayaran</option>
                                <option value="Cash">Cash</option>
                                <option value="Cashless">Cashless</option>
                                
                            </select>
                        </div>
                    </div>
                    {/* ...existing code... */}
                    <div className="user-cart">
                        <div className="card">
                            <table className="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th className="text-right">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {cart.map((c) => (
                                        <tr key={c.id}>
                                            <td>{c.name}</td>
                                            <td>
                                                <input
                                                    type="text"
                                                    className="form-control form-control-sm qty"
                                                    value={c.pivot.quantity}
                                                    onChange={(event) =>
                                                        this.handleChangeQty(
                                                            c.id,
                                                            event.target.value
                                                        )
                                                    }
                                                />
                                                <button
                                                    className="btn btn-danger btn-sm"
                                                    onClick={() =>
                                                        this.handleClickDelete(c.id)
                                                    }
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <td className="text-right">
                                                {window.APP.currency_symbol}
                                                {c.pivot.quantity >= c.minimum_low
                                                    ? (c.pivot.quantity * c.low_price).toFixed(2)
                                                    : (c.pivot.quantity * c.price).toFixed(2)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    {/* ...existing code... */}
                    <div className="row">
                        <div className="col">Total:</div>
                        <div className="col text-right">
                            {window.APP.currency_symbol} {this.getTotal(cart)}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-danger btn-block"
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                            >
                                Batal
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-primary btn-block"
                                disabled={!cart.length}
                                onClick={this.handleClickSubmit}
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
                <div className="col-md-6 col-lg-8">
                    <div className="mb-2">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Cari Produk.."
                            onChange={this.handleChangeSearch}
                            onKeyDown={this.handleSeach}
                        />
                    </div>
                    <div className="order-product">
                        {products.map((p) => (
                            <div
                                onClick={() => this.handleOpenProduct(p.barcode)}
                                key={p.id}
                                className="item"
                            >
                                <img src={p.image_url} alt="" />
                                <h5
                                    style={
                                        window.APP.warning_quantity > p.quantity
                                            ? { color: "red" }
                                            : {}
                                    }
                                >
                                    {p.name}({p.quantity})
                                </h5>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Modal Popup */}
                {showModal && (
                  <div className="modal fade show" tabIndex="-1" role="dialog" style={{ display: "block" }}>
                    <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                        <h5 className="modal-title">Jumlah Produk</h5>
                        <button type="button" className="close" onClick={this.handleCloseProduct}>
                            <span>&times;</span>
                        </button>
                        </div>
                            <form 
                                onSubmit={this.handleSubmit(selectedProduct.id)}
                                encType="multipart/form-data"
                                >
                                <div className="modal-body">
                                    <div className="form-group">
                                        <label>Input Jumlah Produk :</label>
                                        <input 
                                            type="text" 
                                            className="form-control" 
                                            name="inputJumlah"
                                         />
                                    </div>
                                    <p>Sisa Stok : {selectedProduct.quantity}</p>
                                </div>
                                <div className="modal-footer">
                                    <button
                                        
                                        type="button"
                                        className="btn btn-primary"
                                        onClick={this.handleCloseProduct}
                                    >
                                        Tutup
                                    </button>
                                
                                    <button type="submit" className="btn btn-primary">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                )}  
            </div>
        );
    }
}

export default Cart;

if (document.getElementById("cart")) {
    ReactDOM.render(<Cart />, document.getElementById("cart"));
}

$(function() {
	
    function randomNumber(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    };
    $('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));
	
    $('.form-horizontal').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
			name: {
               validators: {
                    notEmpty: {
                        message: 'Enter  Name !'
                    }
                }
            }, 
            email: {
                validators: {
                    notEmpty: {
                        message: 'Enter Email Address !'
                    },
                    emailAddress: {
                        message: 'Enter Valid Email Address !'
                    }
                }
            },
			password: {
                 validators: {
                    notEmpty: {
                        message: 'Enter Password !'
                    },
                    different: {
                        field: 'email',
                        message: 'The password cannot be the same as username'
                    },
					stringLength: {
                        min: 6,
                        max: 30,
                        message: 'The password must be more than 6 characters long'
                    }
                }
            },
			opassword: {
                 validators: {
                    notEmpty: {
                        message: 'Enter Old Password'
                    },
                    different: {
                        field: 'email',
                        message: 'The password cannot be the same as username'
                    },
					stringLength: {
                        min: 6,
                        max: 30,
                        message: 'The password must be more than 6 characters long'
                    }
                }
            },
			cpassword: {
                validators: {
					notEmpty: {
                        message: 'Confirm Your Password !'
                    },
					identical: {
						field: 'password',
						message: 'Password Does not Match !'
					}
				}
            },
			avatars: {
                 validators: {
					file: {
                        extension: 'jpeg,jpg,png',
                        type: 'image/jpeg,image/png',
                        maxSize: 102400,   // 100 kb
                        message: 'Select jpg,jpeg,png less than 100kb File !'
                    }
                }
            },
			brandName: {
				validators: {
                    notEmpty: {
                        message: 'Enter Brand Name !'
                    }
                }
            }, 
			brandAvatar: {
                 validators: {
					 notEmpty: {
                        message: 'Select Image !'
                    },
                    file: {
                        extension: 'jpeg,jpg,png',
                        type: 'image/jpeg,image/png',
                        maxSize: 102400,   // 100 kb
                        message: 'Select jpg,jpeg,png less than 100kb File !'
                    }
                }
            },
			categoryAvatar: {
                 validators: {
					 notEmpty: {
                        message: 'Select Image !'
                    },
                    file: {
                        extension: 'jpeg,jpg,png',
                        type: 'image/jpeg,image/png',
                        maxSize: 102400,   // 100 kb
                        message: 'Select jpg,jpeg,png less than 100kb File !'
                    }
                }
            },
			categoryName: {
				validators: {
                    notEmpty: {
                        message: 'Enter Categoty Name !'
                    }
                }
            },
			productName: {
				validators: {
                    notEmpty: {
                        message: 'Enter Product Name !'
                    }
                }
            }, 
			 
			real_price: {
				validators: {
                    numeric: {
                        message: 'Enter Valid price !'
                    } 
                }
            },
			sale_price: {
				validators: {
                    numeric: {
                        message: 'Enter Valid price !'
                    }
                }
            },
			brand_id: {
				validators: {
                    notEmpty: {
                        message: 'Select Brand!'
                    }
                }
            },
			category_id: {
				validators: {
                    notEmpty: {
                        message: 'Select Categoty!'
                    }
                }
            }
			
			 	
        }
    });
	
	$('.products').find('[name="dealerID[]"]')
		.change(function(e) {
			$('.products').formValidation('revalidateField', 'dealerID[]');
		})
		.end()
		.formValidation({
			framework: 'bootstrap',
			excluded: ':disabled',
			message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
	
        fields: {
			'dealerID[]': {
				validators: {
					callback: {
						message: 'Please select customer',
						callback: function(value, validator, $field) {
							/* Get the selected options */
							var options = validator.getFieldElements('dealerID[]').val();
							return (options != null);
						}
					}
				}
			},
			name: {
				validators: {
                    notEmpty: {
                        message: 'Enter name!'
                    }
                }
            },
			discription: {
				validators: {
                    notEmpty: {
                        message: 'Enter discription!'
                    }
                }
            },
			batch: {
				validators: {
                    notEmpty: {
                        message: 'Enter batch no!'
                    }
                }
            },
			product_staus: {
				validators: {
                    notEmpty: {
                        message: 'Select Product Status !'
                    }
                }
            },
			productName: {
				validators: {
                    notEmpty: {
                        message: 'Enter Product Name !'
                    }
                }
            }, 
			
			brand_id: {
				validators: {
                    notEmpty: {
                        message: 'Select Brand!'
                    }
                }
            },
			category_id: {
				validators: {
                    notEmpty: {
                        message: 'Select Categoty!'
                    }
                }
            }, 
			product_color: {
				validators: {
                    notEmpty: {
                        message: 'Select Color!'
                    }
                }
            },
			productimage:{
				validators: {
					notEmpty: {
						message: 'Select 800x800 Image !'
					},
					file: {
						extension: 'jpeg,jpg,png',
						type: 'image/jpeg,image/png',
						maxSize: 102400,   // 100 kb
						message: 'Select jpg,jpeg,png less than 100kb File !'
					}
				}
			},
			group: {
				validators: {
                    notEmpty: {
                        message: 'Select group!'
                    }
                }
            },
			discountPer: {
				validators: {
                    notEmpty: {
                        message: 'Enter Discount!'
                    }
                }
            },
				 
			real_price: {
				validators: {
                    numeric: {
                        message: 'Enter Valid price !'
                    } 
                }
            },
			sale_price: {
				validators: {
                    numeric: {
                        message: 'Enter Valid price !'
                    }
                }
            }
			
			
        }
    });
	
	
	$('#variation').formValidation({
        message: 'This value is not valid',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
			product_name: {
				validators: {
                    notEmpty: {
                        message: 'Select product name!'
                    }
                }
			},
			stockdate: {
				 validators: {
                    notEmpty: {
                        message: 'The date is required'
                    },
                    date: {
                        format: 'MM/DD/YYYY',
                        message: 'The date is not a valid'
                    }
                }
			},
			'colorStock[]': {
				validators: {
					numeric: {
                        message: 'Enter Valid product Stock !'
                    }
				}
			},
			product_attributes: {
				validators: {
                    notEmpty: {
                        message: 'Select product Attributes!'
                    }
                }
            },
			attr_val: {
				validators: {
                    notEmpty: {
                        message: 'Enter Attribute Value!'
                    }
                }
            },
			product_staus: {
				validators: {
                    notEmpty: {
                        message: 'Select product staus!'
                    }
                }
            },
			batch: {
				validators: {
                    notEmpty: {
                        message: 'Enter batch no!'
                    }
                }
            },
			 
			productStock: {
				validators: {
                    notEmpty: {
                        message: 'enter product Stock!'
                    },
					numeric: {
                        message: 'Enter Valid product Stock !'
                    }
                },
            },
			real_price: {
				validators: {
                    
					numeric: {
                        message: 'Enter Valid price !'
                    },
                }
            },
			sale_price: {
				validators: {
                 	numeric: {
                        message: 'Enter Valid price !'
                    },
                }
            }
			
        }
    });
	
	$('#category_id').change(function (){
		$('#products').formValidation();
		$('#variation').formValidation();
	});
	
});